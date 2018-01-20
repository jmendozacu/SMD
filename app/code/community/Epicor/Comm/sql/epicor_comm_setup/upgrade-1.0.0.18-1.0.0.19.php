<?php
/************************************************************************
Step : Add Attribute to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

// add UOM attribute to products
$installer->addAttribute('catalog_product', 'default_uom', array(
    'group'                         => 'General',
    'label'                         => 'Default UOM',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => true,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'used_in_product_listing'       => true
));


$installer->endSetup();