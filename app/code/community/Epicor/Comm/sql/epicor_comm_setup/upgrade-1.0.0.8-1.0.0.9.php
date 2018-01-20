<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'configurator', array(
    'group' => 'General',
    'type' => 'int',
    'label' => 'Configurator Product',
    'input' => 'boolean',
    'class' => '',
    'default' => 0,
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
    'used_in_product_listing' => true,
));

$installer->endSetup();

