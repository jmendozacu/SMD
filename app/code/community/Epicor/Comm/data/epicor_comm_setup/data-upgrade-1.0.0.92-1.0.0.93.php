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

$writeGroup = $helper->loadAccessGroupByName('Customer - Full Access');

$module = 'Epicor_Comm';
$section = 'Returns';

$element = $helper->addAccessElement($module, $section, 'index', '', 'Access', true);
$element->setPortalExcluded(1);
$element->save();

$element = $helper->addAccessElement($module, $section, 'guestLogin', '', 'Access', true);
$element->setPortalExcluded(1);
$element->save();

$helper->addAccessElement($module, $section, 'view', '', 'Access');
$helper->addAccessElement($module, $section, 'createReturn', '', 'Access');
$helper->addAccessElement($module, $section, 'findReturn', '', 'Access');
$helper->addAccessElement($module, $section, 'updateReference', '', 'Access');
$helper->addAccessElement($module, $section, 'addProduct', '', 'Access');
$helper->addAccessElement($module, $section, 'findProduct', '', 'Access');
$helper->addAccessElement($module, $section, 'saveLines', '', 'Access');
$helper->addAccessElement($module, $section, 'saveAttachments', '', 'Access');
$helper->addAccessElement($module, $section, 'saveNotes', '', 'Access');
$helper->addAccessElement($module, $section, 'saveReview', '', 'Access');
$helper->addAccessElement($module, $section, 'delete', '', 'Access');
$helper->addAccessElement($module, $section, 'list', '', 'Access');
$helper->addAccessElement($module, $section, 'createReturnFromDocument', '', 'Access');

$right = $helper->loadAccessRightByName('Customer - Full Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'view', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'createReturn', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'findReturn', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'updateReference', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'addProduct', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'findProduct', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'saveLines', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'saveAttachments', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'saveNotes', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'saveReview', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'delete', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'list', '', 'Access');
$helper->addAccessRightElement($right->getId(), $module, $section, 'createReturnFromDocument', '', 'Access');

$installer->endSetup();
