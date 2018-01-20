<?php

/**
 * Upgrade - 1.0.6.0.7-1.0.6.0.8
 * 
 * Addinglocaion code to quote lines and order lines
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Quote / Order Item line price

if (!$conn->tableColumnExists($installer->getTable('sales/quote_item'), 'ecc_location_code')) {
    $conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_location_code', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 'comment' => 'ECC Location Code'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/order_item'), 'ecc_location_code')) {
    $conn->addColumn($installer->getTable('sales/order_item'), 'ecc_location_code', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'ECC Location Code'
    ));
}

$installer->endSetup();
