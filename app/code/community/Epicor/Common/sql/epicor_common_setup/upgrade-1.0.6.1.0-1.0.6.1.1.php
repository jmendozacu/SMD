<?php

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->updateAttribute('customer', 'ecc_erp_account_type', 'backend_model', 'epicor_common/eav_attribute_data_erpaccounttype');

$installer->endSetup();
