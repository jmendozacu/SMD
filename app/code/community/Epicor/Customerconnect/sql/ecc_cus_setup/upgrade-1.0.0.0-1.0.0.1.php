<?php

/**
 * customerconnect upgrade 1.0.0.0 to 1.0.0.1
 * 
 *  - Add contact_code / function / telephone_number / fax_number / erp_login_id to customer
 * 
 */
$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('customer', 'contact_code', array(
    'group' => 'General',
    'label' => 'Contact Code',
    'type' => 'text',
    'length' => 255,
    'input' => 'text',
    'default' => '',
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'contact_code', '999'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'contact_code');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->addAttribute('customer', 'function', array(
    'group' => 'General',
    'label' => 'Function',
    'type' => 'text',
    'length' => 255,
    'input' => 'text',
    'default' => '',
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'function', '999'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'function');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->addAttribute('customer', 'telephone_number', array(
    'group' => 'General',
    'label' => 'Telephone Number',
    'type' => 'text',
    'length' => 255,
    'input' => 'text',
    'default' => '',
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'telephone_number', '999'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'telephone_number');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->addAttribute('customer', 'fax_number', array(
    'group' => 'General',
    'label' => 'Fax Number',
    'type' => 'text',
    'length' => 255,
    'input' => 'text',
    'default' => '',
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'fax_number', '999'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'fax_number');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));

$oAttribute->save();

$installer->addAttribute('customer', 'erp_login_id', array(
    'group' => 'General',
    'label' => 'ERP Login ID',
    'type' => 'text',
    'length' => 255,
    'input' => 'text',
    'default' => '',
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'erp_login_id', '999'
);

$installer->endSetup();