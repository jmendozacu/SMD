<?php

/*
 * Update to force p21 sites to use the new Quote reference prefix
 */

$installer = $this;
$installer->startSetup();

if (Mage::getStoreConfig('Epicor_Comm/licensing/erp') == 'p21') {
    $config = Mage::getConfig();
    $config->saveConfig('epicor_quotes/general/prefix', 'Q-');

    $config->reinit();
    Mage::app()->reinitStores();
}

$installer->endSetup();

