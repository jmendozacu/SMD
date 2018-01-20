<?php
/************************************************************************
Step : Add Attribute to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

// add UOM attribute to products
$installer->addAttribute('catalog_product', 'uom_filter', array(
    'group'                         => 'General',
    'label'                         => 'UOM Filter',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => true,
    'comparable'                    => true,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'used_in_product_listing'       => true
));


$installer->endSetup();



