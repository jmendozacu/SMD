<?php

/*
 * Update Script to force p21 to use grid columns when salesrep search for erp accounts
 */

$config = Mage::getConfig()->init();
/* @var $config Mage_Core_Model_Config */

$erp = $config->getStoresConfigByPath('Epicor_Comm/licensing/erp');

if (is_array($erp)) {
    $erp = array_pop($erp);
}

if ($erp == 'p21') {
    $config->saveConfig('epicor_salesrep/masquerade_search/short_code', 0);
    $config->saveConfig('epicor_salesrep/masquerade_search/account_number', 1);
    $config->saveConfig('epicor_salesrep/masquerade_search/invoice_address', 1);
    $config->saveConfig('epicor_salesrep/masquerade_search/default_shipping_address', 0);
    Mage::app()->cleanCache(array('CONFIG'));
}
