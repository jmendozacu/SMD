<?php

/**
 * Version 1.0.0.4 to 1.0.0.5 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$right = $helper->loadAccessRightByName('Customerconnect - Account Information - Access Page');
$element = $helper->loadAccessElement('Mage_Customer', 'Account', 'index', '', 'Access');
$helper->addAccessRightElementById($right->getid(),$element->getId());
$element = $helper->loadAccessElement('Epicor_Common', 'Account', 'index', '', 'Access');
$helper->addAccessRightElementById($right->getid(),$element->getId());

$installer->endSetup();
