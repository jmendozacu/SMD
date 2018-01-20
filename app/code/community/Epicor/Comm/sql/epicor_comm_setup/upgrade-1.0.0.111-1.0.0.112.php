<?php

/**
 * Upgrade - 1.0.0.111 to 1.0.0.112
 * 
 * changing price type
 */
/* * **********************************************************************
  Step : Add Attribute to Products
 * *********************************************************************** */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->updateAttribute(
        'catalog_product', 'price_type', array(
    'frontend_input' => null,
    'frontend_label' => null,
    'is_required' => true,
    'is_user_defined' => false,
    'is_global' => true,
    'is_visible' => false,
    'is_comparable' => false,
    'is_configurable' => false,
    'apply_to' => 'bundle',
        )
);

$installer->removeAttribute('catalog_product', 'ecc_price_display_type');
$installer->addAttribute('catalog_product', 'ecc_price_display_type', array(
    'group' => 'General',
    'label' => 'Price Display Type',
    'type' => 'int',
    'input' => 'select',
    'required' => false,
    'visible' => true,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'option' =>
    array(
        'values' =>
        array(
            0 => 'Default',
            1 => 'Range',
        ),
    ),
));

$installer->endSetup();
