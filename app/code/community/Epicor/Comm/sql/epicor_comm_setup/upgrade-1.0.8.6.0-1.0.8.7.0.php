<?php

/**
 * Set Honor delete flag on STKs for P21
 */

$config = Mage::getConfig()->init();
/* @var $config Mage_Core_Model_Config */

$erp = $config->getStoresConfigByPath('Epicor_Comm/licensing/erp');

if (is_array($erp)) {
    $erp = array_pop($erp);
}

$config->saveConfig('shipping/option/checkout_multiple', 0);

if ($erp == 'p21') {
    $config->saveConfig('epicor_comm_field_mapping/stk_mapping/newproductcode_delete', 1);
}

Mage::app()->cleanCache(array('CONFIG'));