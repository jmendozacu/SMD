<?php

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->addAccessElement('Epicor_Common', 'File', '*', '', 'Access', 1);

$installer->endSetup();

