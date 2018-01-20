<?php

/**
 * Version 1.0.0.79 to 1.0.0.80 upgrade
 * 
 * Adding msq action to access rights
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->addAccessElement('Epicor_Comm', 'Message', '*', '', 'Access', 1);

$installer->endSetup();

