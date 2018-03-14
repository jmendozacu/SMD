<?php

/**
 * Upgrade - 1.0.9.0.1-1.0.9.02
 * 
 * Addinglocaion code to quote address lines
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_location_code')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_location_code', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 
        'comment' => 'ECC Location Code'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_line_comment')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_line_comment', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '64k',
        'comment' => 'ECC Line Comment'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'required_date')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'required_date', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATE,
        'comment' => 'Required Date'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'delivery_deferred')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'delivery_deferred', array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment' => 'Deferred delivery',
        'default' => 0
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_location_name')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_location_name', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 
        'comment' => 'ECC Location Name'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_gqr_line_number')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_gqr_line_number', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 
        'comment' => 'ECC GQR Line Number'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_msq_base_price')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_msq_base_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'ECC MSQ Base Price'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_contract_code')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_contract_code', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 
        'comment' => 'ECC MSQ Base Price'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_salesrep_price')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_salesrep_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'Sales Rep Price'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_salesrep_discount')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_salesrep_discount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'Sales Rep Discount'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote_address_item'), 'ecc_salesrep_rule_price')) {
    $conn->addColumn($installer->getTable('sales/quote_address_item'), 'ecc_salesrep_rule_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'Sales Rep Rule Price'
    ));
}

$installer->endSetup();
