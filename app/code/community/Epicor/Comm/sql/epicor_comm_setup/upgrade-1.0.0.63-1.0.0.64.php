<?php

/**
 * Upgrade - 1.0.0.63 to 1.0.0.64
 * 
 * Add attribut reorderable to products
 */

/************************************************************************
Step : Add Attributes to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'reorderable', array(
    'group'                         => 'General',
    'label'                         => 'Reorderable',
    'type'                          => 'int',    
    'input'                         => 'select',
    'source'                        => 'eav/entity_attribute_source_boolean',
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
