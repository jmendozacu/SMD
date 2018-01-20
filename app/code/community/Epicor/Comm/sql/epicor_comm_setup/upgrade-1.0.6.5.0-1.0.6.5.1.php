<?php

/**
 * Version 1.0.6.5.0 to 1.0.6.5.1 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$right = $helper->loadAccessRightByName('Customer - Full Access');

$element = $helper->addAccessElement('Epicor_Comm', 'Store', 'select', '', 'Access');
$helper->addAccessRightElementById($right->getid(), $element->getId());

$installer->endSetup();