<?php

/**
 * Upgrade - 1.0.6.0.4
 * 
 */
$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();


// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$erpAccountTable = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($erpAccountTable, 'default_location_code')) {
    $conn->addColumn($erpAccountTable, 'default_location_code', array(
        'identity' => false,
        'nullable' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'default' => null,
        'comment' => 'Default Location Code'
    ));
}

$erpAddressTable = $installer->getTable('epicor_comm/customer_erpaccount_address');

if (!$conn->tableColumnExists($erpAddressTable, 'default_location_code')) {
    $conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'default_location_code', array(
        'identity' => false,
        'nullable' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Location Code'
    ));
}


$installer->endSetup();
