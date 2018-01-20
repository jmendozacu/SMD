<?php

/**
 * Upgrade - 1.0.0.76 to 1.0.0.77
 * 
 * Add attribut stocklevel to products
 */

/************************************************************************
Step : Add Attributes to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'stocklevelstyle');
$installer->addAttribute('catalog_product', 'stockleveldisplay', array(
    'group'                         => 'General',
    'label'                         => 'Stock Level',
    'type'                          => 'varchar',    
    'input'                         => 'select',
    'source'                        => 'epicor_comm/eav_attribute_data_stocklevel',
    'required'                      => false,
    'user_defined'                  => false,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'default'                       => '1',
));
$installer->removeAttribute('catalog_product', 'stocklimitlow');
$installer->addAttribute('catalog_product', 'stocklimitlow', array(
    'group'                         => 'General',
    'label'                         => 'Stock Level Limit Amber Indicator',
    'type'                          => 'varchar',    
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => false,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'default'                       => '1',
));
$installer->removeAttribute('catalog_product', 'stocklimitnone');
$installer->addAttribute('catalog_product', 'stocklimitnone', array(
    'group'                         => 'General',
    'label'                         => 'Stock Level Limit Red Indicator',
    'type'                          => 'varchar',    
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => false,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'default'                       => '1',
));
$installer->endSetup();
