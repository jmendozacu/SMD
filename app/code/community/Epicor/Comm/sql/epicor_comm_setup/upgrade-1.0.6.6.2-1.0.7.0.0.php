<?php

/**
 * 1.0.6.6.2-1.0.7.0.0
 *
 * Add erp image fields to category
 */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$entity = 'catalog_category';

$installer->removeAttribute($entity, 'erp_images');
$installer->removeAttribute($entity, 'erp_images_last_processed');
$installer->removeAttribute($entity, 'previous_erp_images');
$installer->removeAttribute($entity, 'erp_images_processed');

$installer->addAttribute($entity, 'erp_images', array(
    'group' => 'General Information',
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
    'input_renderer' => 'epicor_comm/adminhtml_form_element_erpimages_category',
));

$installer->addAttribute($entity, 'erp_images_last_processed', array(
    'group' => 'General Information',
    'label' => 'Last ERP Image process time for this category',
    'type' => 'datetime',
    'input' => 'date',
    'default' => null,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute($entity, 'previous_erp_images', array(
    'group' => 'General Information',
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

$installer->addAttribute($entity, 'erp_images_processed', array(
    'group' => 'General Information',
    'label' => 'Images synced from ERP',
    'type' => 'int',
    'input' => 'select',
    'default' => '0',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'source' => 'eav/entity_attribute_source_boolean'
));

$installer->endSetup();
