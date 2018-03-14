<?php

/**
 *
 *
 * Add new setting for Disable functionality for ERP Accounts under Details tab
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$conn = $installer->getConnection();
$table = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($table, 'disable_functionality')) {
    $conn->addColumn($table, 'disable_functionality', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 20,
        'comment' => 'Disable Functionality, 0: Global Default, cart: Cart, price: Prices, cart_price: Cart & Prices',
        'default' => 0
    ));
}

$installer->endSetup();

/* * **********************************************************************
  Step : Add Attributes to Customers
 * *********************************************************************** */

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

// add column to customer form
$installer->removeAttribute('customer', 'ecc_disable_functionality');
$installer->addAttribute('customer', 'ecc_disable_functionality', array(
    'label' => 'ECC Disable Functionality',
    'type' => 'text',
    'input' => 'select',
    'visible' => true,
    'required' => false,
    'user_defined' => 1,
    'sort_order' => 6,
    'source' => 'epicor_comm/eav_attribute_data_eccdisablefunctionalityoptions',
    'default' => '0',
    "note" => "Disable Customer Functionality based on price / cart / cart&price"
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'ecc_disable_functionality', '12' //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_disable_functionality');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();
$installer->endSetup();
//update all customers to 0 (ERP Account Default)
$customers = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('ecc_disable_functionality');
foreach ($customers as $customer) {
    $customer->setEccDisableFunctionality(0);
    $customer->getResource()->saveAttribute($customer, 'ecc_disable_functionality');
}
