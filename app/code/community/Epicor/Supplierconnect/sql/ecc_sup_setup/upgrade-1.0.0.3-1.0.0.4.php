<?php

/**
 * Upgrade - 1.0.0.3 to 1.0.0.4
 * 
 * Adding previous supplier code to customers
 */

/************************************************************************
Step : Add Attributes to Customers
*************************************************************************/

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 
$installer->addAttribute('customer', 'previous_supplier_erpaccount', array(
    'label' => 'Previous Supplier ERP Account',
    'type' => 'varchar',
    'input' => 'text',
    'visible' => false,
    'required' => false
));

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'previous_supplier_erpaccount',
 '1000'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'previous_supplier_erpaccount');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();