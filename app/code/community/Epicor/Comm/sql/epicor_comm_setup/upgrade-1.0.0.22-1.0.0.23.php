<?php

/**
 * Upgrade - 1.0.0.22 to 1.0.0.23
 * 
 * Adding extra attribute to products for price type
 */
/* * **********************************************************************
  Step : Add Attribute to Products
 * *********************************************************************** */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

// DISARMED 14/08/2015 by GJ - price_type is important and shouldnt be touched!!!

//$installer->removeAttribute('catalog_product', 'price_type');
//$installer->addAttribute('catalog_product', 'price_type', array(
//    'group' => 'General',
//    'label' => 'Price Type',
//    'type' => 'int',
//    'input' => 'select',
//    'required' => false,
//    'user_defined' => true,
//    'searchable' => false,
//    'filterable' => false,
//    'comparable' => true,
//    'visible_on_front' => false,
//    'visible_in_advanced_search' => false,
//    'used_in_product_listing' => true,
//    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//    'option' =>
//    array(
//        'values' =>
//        array(
//            0 => 'Default',
//            1 => 'Range',
//        ),
//    ),
//));


$installer->endSetup();