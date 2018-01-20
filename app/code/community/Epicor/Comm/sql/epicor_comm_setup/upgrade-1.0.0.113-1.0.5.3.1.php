<?php

/**
 * Upgrade - 1.0.5.3.1
 * 
 * Set default customer group
 */

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();


// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->dropColumn($installer->getTable('epicor_comm/erp_customer_group'),'tax_class'); 
$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'tax_class', array(
    'identity' => false,
    'nullable' => FALSE,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Tax Class'
));

$installer->endSetup();