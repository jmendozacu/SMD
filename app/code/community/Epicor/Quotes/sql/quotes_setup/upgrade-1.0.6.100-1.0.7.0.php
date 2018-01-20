<?php
/**
 * Adding lists quote columns
 */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('quotes/quote');

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */

$conn->addColumn(
    $table, 'created_by', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Created By',
    )
);

// Add new quote (ECC GQR) columns
$columnName = 'contract_code';
$columnDefinition = array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'ECC Contract Code'
);

$installHelper->addTableColumn($conn, $installer->getTable('quotes/quote'), $columnName, $columnDefinition);
$installHelper->addTableColumn($conn, $installer->getTable('quotes/quote_product'), $columnName, $columnDefinition);

$installer->endSetup();
