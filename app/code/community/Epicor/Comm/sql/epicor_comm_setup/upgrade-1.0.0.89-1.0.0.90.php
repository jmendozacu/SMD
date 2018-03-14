<?php

/**
 * Install Returns default config on existing system
 */

$config = Mage::getConfig()->init();
/* @var $config Mage_Core_Model_Config */

$erp = $config->getStoresConfigByPath('Epicor_Comm/licensing/erp');

if (is_array($erp)) {
    $erp = array_pop($erp);
}

//set multi shipping to false for all new installs
$config->saveConfig('shipping/option/checkout_multiple', 0);

if ($erp == 'p21' || $erp == 'e10') {
    
    
    $config->saveConfig('epicor_comm_enabled_messages/crrd_request/active', 1);
    $config->saveConfig('epicor_comm_enabled_messages/CRRS_request/active', 1);
    $config->saveConfig('epicor_comm_enabled_messages/CCMS_request/active', 1);
    $config->saveConfig('epicor_comm_enabled_messages/CSNS_request/active', 1);
    $config->saveConfig('epicor_comm_enabled_messages/CRRU_request/active', 1);
    
    $config->saveConfig('epicor_comm_returns/returns/actions', 1);
    
    $config->saveConfig('epicor_comm_returns/guests/enabled', 0);
    $config->saveConfig('epicor_comm_returns/guests/allow_create', 1);
    $config->saveConfig('epicor_comm_returns/guests/find_by', 'return_number,case_number,customer_ref');
    $config->saveConfig('epicor_comm_returns/guests/add_by_sku_enabled', 0);
    $config->saveConfig('epicor_comm_returns/guests/allow_skus_type', '');
    $config->saveConfig('epicor_comm_returns/guests/find_lines_by_enabled', 1);
    $config->saveConfig('epicor_comm_returns/guests/find_lines_by', 'order_number');
    $config->saveConfig('epicor_comm_returns/guests/allow_mixed_return', 0);
    $config->saveConfig('epicor_comm_returns/guests/return_attachments', 1);
    $config->saveConfig('epicor_comm_returns/guests/line_attachments', 1);

    $config->saveConfig('epicor_comm_returns/b2c/enabled', 0);
    $config->saveConfig('epicor_comm_returns/b2c/allow_create', 1);
    $config->saveConfig('epicor_comm_returns/b2c/find_by', 'return_number,case_number,customer_ref');
    $config->saveConfig('epicor_comm_returns/b2c/add_by_sku_enabled', 1);
    $config->saveConfig('epicor_comm_returns/b2c/allow_skus_type', '');
    $config->saveConfig('epicor_comm_returns/b2c/find_lines_by_enabled', 1);
    $config->saveConfig(
        'epicor_comm_returns/b2c/find_lines_by', 'order_number,invoice_number,shipment_number,serial_number'
    );
    $config->saveConfig('epicor_comm_returns/b2c/allow_mixed_return', 1);
    $config->saveConfig('epicor_comm_returns/b2c/return_attachments', 1);
    $config->saveConfig('epicor_comm_returns/b2c/line_attachments', 1);

    $config->saveConfig('epicor_comm_returns/b2b/enabled', 0);
    $config->saveConfig('epicor_comm_returns/b2b/allow_create', 1);
    $config->saveConfig('epicor_comm_returns/b2b/find_by', 'return_number,case_number,customer_ref');
    $config->saveConfig('epicor_comm_returns/b2b/add_by_sku_enabled', 1);
    $config->saveConfig('epicor_comm_returns/b2b/allow_skus_type', '');
    $config->saveConfig('epicor_comm_returns/b2b/find_lines_by_enabled', 1);
    $config->saveConfig(
        'epicor_comm_returns/b2b/find_lines_by', 'order_number,invoice_number,shipment_number,serial_number'
    );
    $config->saveConfig('epicor_comm_returns/b2b/allow_mixed_return', 1);
    $config->saveConfig('epicor_comm_returns/b2b/return_attachments', 1);
    $config->saveConfig('epicor_comm_returns/b2b/line_attachments', 1);
}

Mage::app()->cleanCache(array('CONFIG'));