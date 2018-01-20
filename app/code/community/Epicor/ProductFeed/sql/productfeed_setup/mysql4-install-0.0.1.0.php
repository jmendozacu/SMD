<?php


$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

// add Google Feed attribute to products
$data = array(
    'group'                         => 'General',
    'label'                         => 'Show in Google Product Feed',
    'type'                          => 'int',
    'input'                         => 'boolean',
    'default'                       => '1',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
);
$installer->addAttribute('catalog_product', 'google_feed', $data);


// add Google Feed attribute to products
$data = array(
    'group'                         => 'General',
    'label'                         => 'Condition',
    'type'                          => 'varchar',
    'input'                         => 'select',
    'default'                       => 'new',
    'source'                        => 'productfeed/product_attribute_backend_condition',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
);
$installer->addAttribute('catalog_product', 'condition', $data);

$installer->endSetup();
