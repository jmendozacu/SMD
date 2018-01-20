<?php

/**
 * Upgrade - 1.0.6.1.5 to 1.0.6.1.6
 * 
 * Update attribute oldsku so it appears on flat tables
 */

/************************************************************************
Step : Add Attributes to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();


$installer->updateAttribute('catalog_product', 'oldskus', 'used_in_product_listing', true); // attribute and field to be changed 


$installer->endSetup();

