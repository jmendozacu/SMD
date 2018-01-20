<?php

/**
 * Version 1.0.6.1.3 to 1.0.6.1.4 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->addAccessElement('Epicor_Comm', 'Locations', '*', '', 'Access', 1);

$installer->endSetup();