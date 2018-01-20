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

// - add block name to page_block table

$conn->addColumn(
    $installer->getTable('flexitheme/layout_layout_page'), 'cms_page_identifier',
    array(
        'nullable' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'primary' => false,
        'default' => '',
        'comment' => 'CMS Page URL'
    )
);

// - update data in table to link to block name

$layouts = Mage::getModel('flexitheme/layout')->getCollection();
/* @var $collection Epicor_FlexiTheme_Model_Mysql4_Layout_Collection */

foreach ($layouts->getItems() as $layout) {
    
    $pages = Mage::getModel('flexitheme/layout_layout_page')->getCollection();
    /* @var $collection Epicor_FlexiTheme_Model_Mysql4_Layout_Layout_Page_Collection */
    
    $pages->addFieldToFilter('layout_id', $layout->getId());
    $pages->addFieldToFilter('page_type', 'cms_page');
    
    $pageIds = array();
    
    foreach ($pages->getItems() as $page) {
        $cmsPage = Mage::getModel('cms/page')->load($page->getCmsPageId());
        /* @var $cmsPage Mage_Cms_Model_Page */

        if ($cmsPage->getId() && $cmsPage->getIdentifier() && !in_array($cmsPage->getIdentifier(), $pageIds)) {
            $pageIds[] = $cmsPage->getIdentifier();
            $page->setCmsPageIdentifier($cmsPage->getIdentifier());
            $page->save();
        } else {
            $page->delete();
        }
    }
}

$conn->dropColumn($installer->getTable('flexitheme/layout_layout_page'), 'cms_page_id');

$installer->endSetup();
