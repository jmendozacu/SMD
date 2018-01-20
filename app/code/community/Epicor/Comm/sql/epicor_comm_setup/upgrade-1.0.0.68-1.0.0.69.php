<?php

/**
 * Upgrade - 1.0.0.68 to 1.0.0.69
 * 
 * set default eav)_attribute isRequired to false
 */

$installer = Mage::getResourceModel('customer/setup','customer_setup');
/* @var $installer Mage_Customer_Model_Resource_Setup */
$installer->startSetup();

//Proposed edit is correct: entity is customer_address not customer
$installer->updateAttribute('customer_address','telephone','is_required',false);

$installer->endSetup();