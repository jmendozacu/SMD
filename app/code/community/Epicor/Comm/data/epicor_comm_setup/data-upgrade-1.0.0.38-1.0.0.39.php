<?php

/**
 * Version 1.0.0.38 to 1.0.0.39 upgrade
 * 
 * Ensure 'description' is present in core_config_data path: 'epicor_comm_field_mapping/stt_mapping/product_ecommerce_description'
 */

$installer = $this;
$installer->startSetup();

$update_config = new Mage_Core_Model_Config();
$update_config->saveConfig('epicor_comm_field_mapping/stt_mapping/product_ecommerce_description', 'description');

$installer->endSetup();