<?php

/**
 * Version 1.0.7.1 - 1.0.7.2 upgrade
 *
 */
$installer = $this;
$installer->startSetup();

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */
$installHelper->addAccessElement('Epicor_Customerconnect', 'Recentpurchases', '*', '', 'Access', 1);

$installer->endSetup();
