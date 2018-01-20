<?php

/**
 * Supplierconnect install
 * 
 *  - Add supplier_erpaccount_id to customer
 */
/* * **********************************************************************
  Step : Add Attributes to Customers
 * *********************************************************************** */

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('customer', 'supplier_erpaccount_id', array(
    'group' => 'General',
    'label' => 'Supplier ERP Account',
    'type' => 'int',
    'input' => 'erpaccount',
    'default' => '',
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'supplier_erpaccount_id', '999'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'supplier_erpaccount_id');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();
$installer->endSetup();