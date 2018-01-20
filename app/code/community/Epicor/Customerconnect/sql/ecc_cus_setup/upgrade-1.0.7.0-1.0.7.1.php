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

$module = 'Epicor_Customerconnect';
$section = 'Skus';

$helper->addAccessElement($module, $section, 'index', '', 'Access');
$helper->addAccessElement($module, $section, 'exportToCsv', '', 'Access');
$helper->addAccessElement($module, $section, 'exportToXml', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Skus - List Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'index', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'exportToCsv', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'exportToXml', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());
$installer->endSetup();