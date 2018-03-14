<?php

/**
 * Upgrade - upgrade-1.0.9.0.10-1.0.9.0.11
 * 
 * Adding customer  custom address option 
 */

/************************************************************************
Step : Remove Attributes from Customers and Erp table
*************************************************************************/


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 

// add column to erp account table
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$erpTableName = $installer->getTable('epicor_comm/erp_customer_group');
if ($conn->tableColumnExists($erpTableName, 'allow_shipping_address_edit')) {
    $conn->dropColumn($erpTableName, 'allow_shipping_address_edit');
}
if ($conn->tableColumnExists($erpTableName, 'allow_billing_address_edit')) {
    $conn->dropColumn($erpTableName, 'allow_billing_address_edit');
}


$customerShippingAddressEdit = $installer->getAttribute('customer','ecc_allow_ship_address_edit');
if(!empty($customerShippingAddressEdit)) {
   $installer->removeAttribute( 'customer', 'ecc_allow_ship_address_edit' ); 
}

$customerBillingAddressEdit = $installer->getAttribute('customer','ecc_allow_bill_address_edit');
if(!empty($customerBillingAddressEdit)) {
   $installer->removeAttribute( 'customer', 'ecc_allow_bill_address_edit' ); 
}

$installer->endSetup();