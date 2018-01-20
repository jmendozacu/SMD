<?php

/**
 * Version 1.0.0.9 to 1.0.0.10 upgrade
 * 
 * Adds excluded elements for access rights 
 */

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

// create default exclusions
$module = 'Epicor_Customerconnect';

// set up access right

$readWriteGroup = $helper->loadAccessGroupByName('Customerconnect - Full Access');

$helper->addAccessElement($module, 'Account', 'saveErpBillingAddress', '', 'Access');

// set up rights in access right
$right = $helper->addAccessRight('Customerconnect - Account Information - Save New Checkout Addresses - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Account','saveErpBillingAddress','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$installer->endSetup();