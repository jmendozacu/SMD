<?php

/**
 * Version 1.0.0.17 to 1.0.0.18 upgrade
 * 
 * Adds RFQ to access rights
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$writeGroup = $helper->loadAccessGroupByName('Customerconnect - Full Access');
$readGroup = $helper->loadAccessGroupByName('Customerconnect - Read Only');

$module = 'Epicor_Customerconnect';
$section = 'Returns';

$helper->addAccessElement($module, $section, 'index', '', 'Access');
$helper->addAccessElement($module, $section, 'exportCsv', '', 'Access');
$helper->addAccessElement($module, $section, 'exportXml', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Returns - List Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'index', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'exportCsv', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'exportXml', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());

$helper->addAccessElement($module, $section, 'details', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Returns - Details Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'details', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());

$installer->endSetup();
