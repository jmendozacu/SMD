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
$section = 'Rfqs';

$helper->addAccessElement($module, $section, 'index', '', 'Access');
$helper->addAccessElement($module, $section, 'exportCsv', '', 'Access');
$helper->addAccessElement($module, $section, 'exportXml', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Rfqs - List Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'index', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'exportCsv', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'exportXml', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());

$helper->addAccessElement($module, $section, 'details', '', 'Access');
$helper->addAccessElement($module, $section, 'addressdetails', '', 'Access');
$helper->addAccessElement($module, $section, 'lineaddautocomplete', '', 'Access');
$helper->addAccessElement($module, $section, 'linesearch', '', 'Access');
$helper->addAccessElement($module, $section, 'importProductCsv', '', 'Access');
$helper->addAccessElement($module, $section, 'ewacomplete', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Rfqs - Details Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, 'details', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'addressdetails', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'lineaddautocomplete', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'linesearch', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'importProductCsv', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'ewacomplete', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());


$helper->addAccessElement($module, $section, 'new', '', 'Access');
$helper->addAccessElement($module, $section, 'add', '', 'Access');
$helper->addAccessElement($module, $section, 'update', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Rfqs - Add/Edit');
$helper->addAccessRightElement($right->getId(), $module, $section, 'new', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'add', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'update', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());

$helper->addAccessElement($module, $section, 'confirmreject', '', 'Access');
$helper->addAccessElement($module, $section, 'confirm', '', 'Access');
$helper->addAccessElement($module, $section, 'reject', '', 'Access');

$right = $helper->addAccessRight('Customerconnect - Rfqs - Confirm/Reject');
$helper->addAccessRightElement($right->getId(), $module, $section, 'confirmreject', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'confirm', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'reject', '', 'Access');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());


$installer->endSetup();
