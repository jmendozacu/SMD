<?php

/**
 * Version 1.0.9.0.12 to 1.0.9.0.13 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$right = $helper->loadAccessRightByName('Customer - Full Access');

$element = $helper->addAccessElement('Epicor_Comm', 'Customer_address', 'saveDefaultAddress', '', 'Access');
$helper->addAccessRightElementById($right->getid(), $element->getId());

$installer->endSetup();