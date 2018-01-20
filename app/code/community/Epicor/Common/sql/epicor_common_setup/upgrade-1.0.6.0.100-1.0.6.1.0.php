<?php

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('customer', 'ecc_erp_account_type', array(
    'group' => 'General',
    'label' => 'ERP Account Type',
    'type' => 'text',
    'input' => 'text',
    'frontend_model' => 'epicor_common/eav_entity_attribute_frontend_erpaccounttype',
    'default' => '',
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->updateAttribute('customer', 'ecc_erp_account_type', 'frontend_model', 'epicor_common/eav_entity_attribute_frontend_erpaccounttype');

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'ecc_erp_account_type', '1000'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_erp_account_type');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();
