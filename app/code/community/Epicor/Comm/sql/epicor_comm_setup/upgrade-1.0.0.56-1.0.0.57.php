<?php

/**
 * Upgrade - 1.0.0.54 to 1.0.0.55
 * 
 * Add attribut oldsku to products
 */

/************************************************************************
Step : Add Attributes to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'oldskus', array(
    'group'                         => 'General',
    'label'                         => 'Old Skus',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));
$installer->endSetup();
