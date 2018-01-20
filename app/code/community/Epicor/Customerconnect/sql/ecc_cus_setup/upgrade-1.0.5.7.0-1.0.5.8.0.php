<?php

/**
 * Version 1.0.5.7 to 1.0.5.8 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$right = $helper->loadAccessRightByName('Customerconnect - Account Information - Contacts - Edit');
$element = $helper->addAccessElement('Epicor_Customerconnect', 'Account', 'syncContact', '', 'Access');
$helper->addAccessRightElementById($right->getid(),$element->getId());

$installer->endSetup();