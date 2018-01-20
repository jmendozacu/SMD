<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'uom', 'used_in_product_listing', true);

$installer->endSetup();

