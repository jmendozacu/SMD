<?php

// Add customer default location

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

// add column to erp account table

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$tableName = $installer->getTable('epicor_comm/location_product');
if ($conn->tableColumnExists($tableName, 'free_stock')) {
    $conn->changeColumn($tableName, 'free_stock', 'free_stock', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '16,4',
        'identity' => false,
        'nullable' => true,
        'primary' => false,
    ));
}

if ($conn->tableColumnExists($tableName, 'minimum_order_qty')) {
    $conn->changeColumn($tableName, 'minimum_order_qty', 'minimum_order_qty', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '16,4',
        'identity' => false,
        'nullable' => true,
        'primary' => false,
    ));
}

if ($conn->tableColumnExists($tableName, 'maximum_order_qty')) {
    $conn->changeColumn($tableName, 'maximum_order_qty', 'maximum_order_qty', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '16,4',
        'identity' => false,
        'nullable' => true,
        'primary' => false,
    ));
}


$tableName = $installer->getTable('epicor_comm/location_product_currency');
if ($conn->tableColumnExists($tableName, 'cost_price')) {
    $conn->changeColumn($tableName, 'cost_price', 'cost_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '16,4',
        'identity' => false,
        'nullable' => true,
        'primary' => false,
    ));
}
if ($conn->tableColumnExists($tableName, 'base_price')) {
    $conn->changeColumn($tableName, 'base_price', 'base_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '16,4',
        'identity' => false,
        'nullable' => true,
        'primary' => false,
    ));
}

$installer->endSetup();
