<?php

/**
 * Upgrade - 1.0.0.10 to 1.0.0.11
 * 
 * extending message log table xml columns
 */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'related_documents');
$installer->addAttribute('catalog_product', 'related_documents', array(
    'group' => 'General',
    'type' => 'text',
    'backend' => 'epicor_comm/eav_attribute_data_relateddocuments',
    'input_renderer' => 'epicor_comm/adminhtml_form_element_relateddocuments', //definition of renderer
    'label' => 'Related Documents',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
));

$installer->removeAttribute('catalog_product', 'erp_images');
$installer->addAttribute('catalog_product', 'erp_images', array(
    'group' => 'Images',
    'type' => 'text',
    'backend' => 'eav/entity_attribute_backend_serialized',
    'label' => 'ERP Images',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
));
$installer->endSetup();
