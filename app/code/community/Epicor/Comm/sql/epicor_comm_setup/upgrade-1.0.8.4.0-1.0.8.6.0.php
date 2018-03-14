<?php

/**
 * Adding Default config for B2c msq based on existsing settings
 */
set_time_limit(0);

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$table = $installer->getTable('core_config_data');

$paths = array(
    'epicor_comm_enabled_messages/msq_request/active',
    'epicor_comm_enabled_messages/msq_request/msq_quantity_breaks',
    'epicor_comm_enabled_messages/msq_request/products_always_in_stock',
    'epicor_comm_enabled_messages/msq_request/cusomterpriceused',
    'epicor_comm_enabled_messages/msq_request/update_prices_stock'
);

foreach ($paths as $path) {
    $installer->run("
        INSERT IGNORE INTO `" . $table . "`
            (`scope`,`scope_id`,`path`,`value`) 
        SELECT
            conf1.scope, 
            conf1.scope_id, 
            REPLACE(conf1.path, 'epicor_comm_enabled_messages/msq_request/','epicor_comm_enabled_messages/msq_request/b2c_'), 
            conf1.value 
        FROM `" . $table . "` as conf1
        WHERE conf1.path = '" . $path . "'
    ");
}

$paths = array(
    'epicor_comm_enabled_messages/msq_request/products_always_in_stock',
);

foreach ($paths as $path) {
    $installer->run("
        INSERT IGNORE INTO `" . $table . "`
            (`scope`,`scope_id`,`path`,`value`) 
        SELECT
            conf1.scope, 
            conf1.scope_id, 
            REPLACE(conf1.path, 'epicor_comm_enabled_messages/msq_request/','epicor_comm_field_mapping/stk_mapping/'), 
            conf1.value 
        FROM `" . $table . "` as conf1
        WHERE conf1.path = '" . $path . "'
    ");
}

$installer->endSetup();
