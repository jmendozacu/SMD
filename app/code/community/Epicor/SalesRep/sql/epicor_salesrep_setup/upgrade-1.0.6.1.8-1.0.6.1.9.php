<?php

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->updateAttribute('customer', 'sales_rep_account_id', 'frontend_model', null);
$installer->updateAttribute('customer', 'sales_rep_account_id', 'frontend_input', 'text');
$installer->updateAttribute('customer', 'sales_rep_account_id', 'is_visible', false);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'sales_rep_account_id');
$oAttribute->setData('used_in_forms', array());
$oAttribute->save();

$installer->endSetup();
