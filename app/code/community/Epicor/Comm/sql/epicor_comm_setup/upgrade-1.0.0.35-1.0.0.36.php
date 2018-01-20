<?php

/**
 * Upgrade - 1.0.0.35 to 1.0.0.36
 * 
 * UPdating ERP images attribute
 */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'all_store_erp_images_processed');
$installer->removeAttribute('catalog_product', 'store_erp_images_processed');
$installer->removeAttribute('catalog_product', 'base_erp_images_processed');

$installer->addAttribute('catalog_product', 'erp_images', array(
    'group' => 'Images',
    'type' => 'text',
    'backend' => 'eav/entity_attribute_backend_serialized',
    'label' => 'ERP Images',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'input_renderer' => 'epicor_comm/adminhtml_form_element_erpimages',
));

$installer->addAttribute('catalog_product', 'previous_erp_images', array(
    'group' => 'Images',
    'type' => 'text',
    'backend' => 'eav/entity_attribute_backend_serialized',
    'label' => 'Previous ERP Images',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
));

$installer->addAttribute('catalog_product', 'erp_images_processed', array(
    'group' => 'Images',
    'label' => 'Images synced from ERP',
    'type' => 'int',
    'input' => 'boolean',
    'default' => '0',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));

$installer->endSetup();