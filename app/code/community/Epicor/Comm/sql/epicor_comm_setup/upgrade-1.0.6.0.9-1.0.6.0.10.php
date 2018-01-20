<?php

/**
 * Upgrade - 1.0.6.0.9-1.0.6.0.10
 * 
 * Adding location name and gqr line number to quote lines and order lines
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// ECC Location Name 

if (!$conn->tableColumnExists($installer->getTable('sales/quote_item'), 'ecc_location_name')) {
    $conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_location_name', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 'comment' => 'ECC Location Name'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/order_item'), 'ecc_location_name')) {
    $conn->addColumn($installer->getTable('sales/order_item'), 'ecc_location_name', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'ECC Location Name'
    ));
}

// GQR Line number
if (!$conn->tableColumnExists($installer->getTable('sales/quote_item'), 'ecc_gqr_line_number')) {
    $conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_gqr_line_number', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 'comment' => 'ECC GQR Line Number'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/order_item'), 'ecc_gqr_line_number')) {
    $conn->addColumn($installer->getTable('sales/order_item'), 'ecc_gqr_line_number', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'ECC GQR Line Number'
    ));
}

$installer->endSetup();
