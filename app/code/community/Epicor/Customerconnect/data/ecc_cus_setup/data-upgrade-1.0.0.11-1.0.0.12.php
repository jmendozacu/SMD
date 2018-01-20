<?php

/**
 * Version 1.0.0.11 to 1.0.0.12 upgrade
 * 
 * Adds invoice & shipments reordering to access rights
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$readWriteGroup = $helper->loadAccessGroupByName('Customerconnect - Full Access');

$module = 'Epicor_Customerconnect';

$helper->addAccessElement($module, 'Invoices', 'reorder', '', 'Access');
$helper->addAccessElement($module, 'Shipments', 'reorder', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Invoices - Re-order');
$helper->addAccessRightElement($right->getId(), $module, 'Invoices', 'reorder', '', 'Access');
$helper->addAccessGroupRight($readWriteGroup->getId(), $right->getId());

$right = $helper->addAccessRight('Customerconnect - Shipments - Re-order');
$helper->addAccessRightElement($right->getId(), $module, 'Shipments', 'reorder', '', 'Access');
$helper->addAccessGroupRight($readWriteGroup->getId(), $right->getId());

$installer->endSetup();
