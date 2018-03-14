<?php

/**
 * Upgrade - 1.0.9.0.0-1.0.9.0.1
 * 
 * 
 */

/************************************************************************
Step : Update the configuration into core_config_data
*************************************************************************/

$installer = $this;
$installer->startSetup();

$installer->deleteConfigData('epicor_comm_field_mapping/cus_mapping/allow_shipping_address_edit');
$installer->deleteConfigData('epicor_comm_field_mapping/cus_mapping/allow_billing_address_edit');
$installer->deleteConfigData('epicor_comm_field_mapping/cus_mapping/allow_shipping_address_create');
$installer->deleteConfigData('epicor_comm_field_mapping/cus_mapping/allow_billing_address_create');

$table = $installer->getTable('core_config_data');

$paths = array(
    'epicor_comm_field_mapping/cus_mapping/cus_create_addresses'
);

foreach ($paths as $path) {
   $installer->run("
        INSERT IGNORE INTO `" . $table . "`
            (`scope`,`scope_id`,`path`,`value`) 
        SELECT
            conf1.scope, 
            conf1.scope_id, 
            'epicor_comm_field_mapping/cus_mapping/allow_shipping_address_create', 
            conf1.value 
        FROM `" . $table . "` as conf1
        WHERE conf1.path = '" . $path . "'
    ");
}

$paths = array(
    'epicor_comm_field_mapping/cus_mapping/cus_create_addresses'
);

foreach ($paths as $path) {
   $installer->run("
        INSERT IGNORE INTO `" . $table . "`
            (`scope`,`scope_id`,`path`,`value`) 
        SELECT
            conf1.scope, 
            conf1.scope_id, 
            'epicor_comm_field_mapping/cus_mapping/allow_billing_address_create', 
            conf1.value 
        FROM `" . $table . "` as conf1
        WHERE conf1.path = '" . $path . "'
    ");
}

$installer->endSetup();