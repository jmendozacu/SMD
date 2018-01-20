<?php

/**
 * Version 0.1.1-0.1.2
 *
 * Adds Branch pickup into access rights
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$model = Mage::getResourceModel('epicor_common/access_element');
/* @var $model Epicor_Common_Model_Resource_Access_Element */

$right   = $helper->loadAccessRightByName('Customer - Full Access ');
$element = $helper->addAccessElement('Epicor_BranchPickup', '*', '*', '', 'Access');
$helper->addAccessRightElementById($right->getid(), $element->getId());


$module  = 'Epicor_BranchPickup';
$section = 'Pickup';
$helper->addAccessElement($module, $section, 'select', '', 'Access');
$helper->addAccessElement($module, $section, 'selectBranch', '', 'Access');
$helper->addAccessElement($module, $section, 'pickupsearch', '', 'Access');
$helper->addAccessElement($module, $section, 'pickupsearchgrid', '', 'Access');
$helper->addAccessElement($module, $section, 'selectBranch', '', 'Access');
$helper->addAccessElement($module, $section, 'selectBranchAjax', '', 'Access');
$helper->addAccessElement($module, $section, 'changePickupLocation', '', 'Access');
$helper->addAccessElement($module, $section, 'removebranchpickup', '', 'Access');
$helper->addAccessElement($module, $section, 'cartPopup', '', 'Access');
$helper->addAccessElement($module, $section, 'removeItemsInCart', '', 'Access');
$helper->addAccessElement($module, $section, 'saveLocationQuote', '', 'Access');
$helper->addAccessElement($module, $section, 'saveLocation', '', 'Access');


$right = $helper->loadAccessRightByName('Customerconnect - Branch Pickup - List Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'select', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'selectBranch', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'pickupsearch', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'pickupsearchgrid', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'selectBranch', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'selectBranchAjax', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'changePickupLocation', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'removebranchpickup', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'cartPopup', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'removeItemsInCart', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'saveLocationQuote', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'saveLocation', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'removeItemsInCart', '', 'Access');


$installer->endSetup();