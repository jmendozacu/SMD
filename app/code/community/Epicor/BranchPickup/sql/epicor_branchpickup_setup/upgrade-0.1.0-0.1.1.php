<?php

/**
 * Version upgrade-1.0.7.0-1.0.7.1 upgrade
 * 
 * 
 * Adds SKUs to access rights
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$writeGroup = $helper->loadAccessGroupByName('Customerconnect - Full Access');
$readGroup  = $helper->loadAccessGroupByName('Customerconnect - Read Only');

$module = 'Epicor_BranchPickup';
$section = 'Pickup';

$helper->addAccessElement($module, $section, 'select', '', 'Access');
$helper->addAccessElement($module, $section, 'selectBranch', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Branch Pickup - List Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'select', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'selectBranch', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());


$helperCustomer = Mage::helper('epicor_common/setup');
$helperCustomer->addAccessElement($module, $section, 'select', '', 'Access');
$helperCustomer->addAccessElement($module, $section, 'selectBranch', '', 'Access');
$writeCgroup   = $helperCustomer->loadAccessGroupByName('Customer - Full Access');
$rightCustomer = $helperCustomer->addAccessRight('Customer - Branch Pickup - List Page - View');
$helperCustomer->addAccessRightElement($rightCustomer->getId(), $module, $section, 'select', '', 'Access');
$helperCustomer->addAccessRightElement($rightCustomer->getId(), $module, $section, 'selectBranch', '', 'Access');
$helperCustomer->addAccessGroupRight($writeCgroup->getId(), $rightCustomer->getId());

$installer->endSetup();