<?php

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->updateAttribute('customer', 'sales_rep_account_id', 'frontend_input', 'text');
$installer->updateAttribute('customer', 'sales_rep_account_id', 'frontend_model', 'epicor_salesrep/eav_entity_attribute_frontend_salesrepaccount');

$installer->endSetup();
