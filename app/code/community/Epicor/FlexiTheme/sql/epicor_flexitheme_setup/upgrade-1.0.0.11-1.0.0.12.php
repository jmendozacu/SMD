<?php

/* * **********************************************************************
  Step : Update layout
  - loop through all blocks and ensure no dupe names
  - add unique index to block name column
  - add block name to page_block table
  - add FK on page block to block table
  - update data in table to link to block name
  - remove block id column
 * *********************************************************************** */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


$blockModel = Mage::getModel('flexitheme/layout_block');
/* @var $blockModel Epicor_FlexiTheme_Model_Layout_Block */

$collection = $blockModel->getCollection();
/* @var $collection Epicor_FlexiTheme_Model_Mysql4_Layout_Block_Collection */

$names = array();

//  - loop through all blocks and ensure no dupe names

foreach ($collection->getItems() as $block) {
    $level = 1;
    $unique = false;
    while ($unique == false) {
        $name = $block->getBlockName();
        if (in_array($name, $names)) {
            $name .= ' - ' . $level;
            if (in_array($name, $names)) {
                $level++;
            } else {
                $unique = true;
            }
        } else {
            $unique = true;
        }
    }

    $names[] = $name;
    $block->setBlockName($name);
    $block->save();
}

// - add unique index to block name column


$conn->addKey(
    $installer->getTable('flexitheme/layout_block'), 'block_name_unique_idx', 'block_name', 'UNIQUE'
);

// - add block name to page_block table

$conn->addColumn(
    $installer->getTable('flexitheme/layout_page_block'), 'block_name',
    array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'primary' => false,
    'default' => '',
    'comment' => 'Block name'
    )
);

// - update data in table to link to block name

$collection = Mage::getModel('flexitheme/layout_page_block')->getCollection();
/* @var $collection Epicor_FlexiTheme_Model_Mysql4_Layout_Page_Block_Collection */

foreach ($collection->getItems() as $pageBlock) {
    /* @var $pageBlock Epicor_FlexiTheme_Model_Layout_Page_Block */
    $block = Mage::getModel('flexitheme/layout_block')->load($pageBlock->getBlockId());
    /* @var $block Epicor_FlexiTheme_Model_Layout_Block */
    $pageBlock->setBlockName($block->getBlockName());
    $pageBlock->save();
}

// - add FK on page block to block table

$conn->addForeignKey(
    $installer->getFkName('flexitheme/layout_block', 'block_name','flexitheme/layout_page_block', 'block_name'),
    $installer->getTable('flexitheme/layout_page_block'),
    'block_name',
    $installer->getTable('flexitheme/layout_block'),
    'block_name',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

// - remove block id column

$conn->dropColumn($installer->getTable('flexitheme/layout_page_block'), 'block_id');

$installer->endSetup();
