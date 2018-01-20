<?php
/**
 * Upgrade 1.0.0.0 to 1.0.0.1
 * 
 * Adds last_msq_update to products
 * 
 */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'last_msq_update');

$installer->addAttribute('catalog_product', 'last_msq_update', array(
    'group'                         => 'General',
    'label'                         => 'Last update from ERP',
    'type'                          => 'datetime',
    'input'                         => 'date',
    'required'                      => false,
    'user_defined'                  => false,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));