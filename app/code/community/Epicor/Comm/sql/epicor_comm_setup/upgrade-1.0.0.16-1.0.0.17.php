<?php

/**
 * Upgrade - 1.0.0.16 to 1.0.0.17
 * 
 * removing old erp image attributes and adding new ones in
 */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();


$installer->removeAttribute('catalog_product', 'erp_image_main');
$installer->removeAttribute('catalog_product', 'erp_image_thumb');
$installer->removeAttribute('catalog_product', 'ftp_image_assigned');
$installer->removeAttribute('catalog_product', 'ftp_image_checked');
$installer->removeAttribute('catalog_product', 'ftp_image_id_main');
$installer->removeAttribute('catalog_product', 'ftp_image_id_thumb');


$installer->removeAttribute('catalog_product', 'erp_images_last_processed');
$installer->removeAttribute('catalog_product', 'base_erp_images_processed');
$installer->removeAttribute('catalog_product', 'all_store_erp_images_processed');
$installer->removeAttribute('catalog_product', 'store_erp_images_processed');
$installer->removeAttribute('catalog_product', 'previous_erp_images');

$installer->addAttribute('catalog_product', 'previous_erp_images', array(
    'group' => 'Images',
    'type' => 'text',
    'backend' => 'eav/entity_attribute_backend_serialized',
    'label' => 'Previous ERP Images',
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
));

$installer->addAttribute('catalog_product', 'erp_images_last_processed', array(
    'group'                         => 'Images',
    'label'                         => 'Last ERP Image process time for this product',
    'type'                          => 'datetime',
    'input'                         => 'date',
    'default'                       => null,
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'base_erp_images_processed', array(
    'group' => 'Images',
    'label' => 'Images processed from ERP (Base Product)',
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
));

$installer->addAttribute('catalog_product', 'all_store_erp_images_processed', array(
    'group' => 'Images',
    'label' => 'Images processed from ERP (All Stores)',
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
));

$installer->addAttribute('catalog_product', 'store_erp_images_processed', array(
    'group' => 'Images',
    'label' => 'Images processed from ERP (Store View)',
    'type' => 'int',
    'input' => 'boolean',
    'default' => '0',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
));


$installer->endSetup();

