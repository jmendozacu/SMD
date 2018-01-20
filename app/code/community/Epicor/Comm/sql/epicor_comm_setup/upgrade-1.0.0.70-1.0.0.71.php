<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

/* @var $installer Mage_Catalog_Model_Resource_Setup */


/* * **********************************************************************
  Step : Create Category and Product is_new attribute
 * *********************************************************************** */


$installer->removeAttribute('catalog_product', 'is_new');
$installer->addAttribute('catalog_product', 'is_new', array(
    'group' => 'General',
    'label' => 'Is New',
    'type' => 'int',
    'input' => 'boolean',
    'required' => false,
    'user_defined' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'default' => 1
));

$installer->removeAttribute('catalog_category', 'is_new');
$installer->addAttribute('catalog_category', 'is_new', array(
    'group' => 'General',
    'label' => 'Is New',
    'type' => 'int',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'input'    => 'select',
    'source'   => 'eav/entity_attribute_source_boolean',
    'required' => false,
    'user_defined' => false,
    'visible' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'default' => 1
));
