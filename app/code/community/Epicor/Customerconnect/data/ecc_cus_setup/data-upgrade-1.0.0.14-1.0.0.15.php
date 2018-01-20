<?php

/**
 * Version 1.0.0.14 to 1.0.0.15 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

// set up access right
$element1 = $helper->addAccessElement('Epicor_Customerconnect', 'Account', 'index', 'contact_address_permissions',
    'view');
$element2 = $helper->addAccessElement('Epicor_Customerconnect', 'Account', 'saveCustomAddressAllowed', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Account Information - Contact Address Settings - Edit');

$manageGroup = $helper->loadAccessGroupByName('Customerconnect - Manage Permissions');

$helper->addAccessRightElementById($right->getid(), $element1->getId());
$helper->addAccessRightElementById($right->getid(), $element2->getId());
$helper->addAccessGroupRight($manageGroup->getId(), $right->getId());

$installer->endSetup();
