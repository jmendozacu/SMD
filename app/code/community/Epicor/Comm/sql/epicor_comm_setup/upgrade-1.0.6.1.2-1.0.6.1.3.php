<?php

/**
 * Version 1.0.6.1.2 to 1.0.6.1.3 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$right = $helper->loadAccessRightByName('Customer - Full Access');
$element = $helper->addAccessElement('Epicor_Comm', 'Locations', 'addToCartFromMyOrdersWidget', '', 'Access');
$helper->addAccessRightElementById($right->getid(),$element->getId());

$installer->endSetup();