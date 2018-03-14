<?php

/**
 * Version 1.0.9.0.10 - 1.0.9.0.11 upgrade
 *
 */
$installer = $this;
$installer->startSetup();

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */
$installHelper->addAccessElement('Epicor_Common', 'Sales_order', 'nonErpProductCheck', '', 'Access', 1);

$installer->endSetup();
