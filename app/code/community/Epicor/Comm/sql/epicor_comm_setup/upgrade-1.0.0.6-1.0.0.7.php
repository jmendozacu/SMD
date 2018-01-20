<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'related_documents', array(
    'group' => 'General',
    'type' => 'text',
    'backend' => 'epicor_comm/eav_attribute_data_relateddocuments',
    'input_renderer' => 'epicor_comm/adminhtml_form_element_relateddocuments', //definition of renderer
    'label' => 'Related Documents',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
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

$installer->addAttribute('catalog_product', 'erp_images', array(
    'group' => 'Images',
    'type' => 'text',
    'backend' => 'eav/entity_attribute_backend_serialized',
    'label' => 'ERP Images',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
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

