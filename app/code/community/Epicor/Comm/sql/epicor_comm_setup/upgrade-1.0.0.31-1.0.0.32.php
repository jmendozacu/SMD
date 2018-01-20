<?php

/**
 * Upgrade - 1.0.0.31 to 1.0.0.32
 * 
 * Creating manufacturers attribute
 */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'manufacturer', 'is_visible', '0');
$installer->removeAttribute('catalog_product', 'manufacturers');
$installer->addAttribute('catalog_product', 'manufacturers', array(
    'group' => 'General',
    'label' => 'Manufacturers',
    'type' => 'text',
    'input' => 'multiselect',
    'backend' => 'eav/entity_attribute_backend_serialized',
    'input_renderer' => 'epicor_comm/adminhtml_form_element_manufacturers', //definition of renderer
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


$installer->endSetup();