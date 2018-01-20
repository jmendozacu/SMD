<?php

// Add customer default location

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$installer->addAttribute('customer', 'ecc_default_location_code', array(
    'group' => 'General',
    'label' => 'Default Location Code',
    'type' => 'varchar',
    'input' => 'text',
    'default' => '',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'user_defined' => 0,
    'sort_order' => 126,
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'ecc_default_location_code', '126'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_default_location_code');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$installer->endSetup();
