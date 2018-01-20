<?php

/**
 * Upgrade - 1.0.0.109 to 1.0.0.110
 * 
 * Adding extra columns to product options table
 */

/* * **********************************************************************
  Step : Add Attributes to Customers
 * *********************************************************************** */


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();


// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
//$conn->dropColumn($installer->getTable('epicor_comm/erp_customer_group'),'custom_address_allowed'); 
$conn->addColumn($installer->getTable('catalog/product_option'), 'epicor_code', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'default' => null,
    'length' => 255,
    'comment' => 'Code From ERP'
));

$conn->addColumn($installer->getTable('catalog/product_option'), 'epicor_default_value', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'default' => null,
    'length' => 255,
    'comment' => 'Default Value'
));

$conn->addColumn($installer->getTable('catalog/product_option'), 'epicor_validation_code', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'default' => null,
    'length' => 255,
    'comment' => 'Validation Code'
));


$installer->endSetup();
