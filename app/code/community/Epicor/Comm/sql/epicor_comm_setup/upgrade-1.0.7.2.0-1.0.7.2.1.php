<?php

/**
 * Version 1.0.7.2.0 - 1.0.7.2.1 upgrade
 *
 */
$installer = $this;
$installer->startSetup();

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */
$installHelper->addAccessElement('Epicor_Lists', '*', '*', '', 'Access', 1);

$installer->endSetup();
