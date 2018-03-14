<?php

/**
 * Upgrade - 1.0.9.0.2 to 1.0.9.0.3
 * 
 * Adding ecc_related_documents_synced product attribute 
 */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'ecc_related_documents_synced', array(
    'group' => 'General',
    'label' => 'ECC Related Documents Synced',
    'type' => 'int',
    'input' => 'boolean',
    'default' => '0',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'sort_order' => 29
));