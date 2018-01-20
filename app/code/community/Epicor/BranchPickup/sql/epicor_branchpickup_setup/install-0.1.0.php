<?php

/**
 * 
 *
 * Add new setting for branch pickup allowed for ERP Accounts Details tab
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($table, 'is_branch_pickup_allowed')) {
    $conn->addColumn($table, 'is_branch_pickup_allowed', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'comment' => 'Branch Pickup Allowed, 2: Default, 0: Disabled, 1: Enabled',
        'default' => 2
    ));
}

$installer->endSetup();

/************************************************************************
Step : Add Attributes to Customers
*************************************************************************/

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

// add column to customer form 
$installer->removeAttribute('customer', 'is_branch_pickup_allowed');
$installer->addAttribute('customer', 'is_branch_pickup_allowed', array(
    'label' => 'Branch Pickup Allowed',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 1,
    'sort_order' => 6,
    'source' => 'epicor_branchpickup/eav_attribute_data_branchoptions',
    'default' => '2'
));

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'is_branch_pickup_allowed', '11' //sort_order
    );

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'is_branch_pickup_allowed');
$oAttribute->setData('used_in_forms', array(
    'adminhtml_customer'
));
$oAttribute->save();
$installer->endSetup();
//update all customers to 2 (ERP Account Default)
$customers = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('is_branch_pickup_allowed');
foreach ($customers as $customer) {
    $customer->setIsBranchPickupAllowed(2);
    $customer->getResource()->saveAttribute($customer, 'is_branch_pickup_allowed');
}