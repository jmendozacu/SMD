<?php

/**
 * Version 1.0.0.7 to 1.0.0.8 upgrade
 * 
 * Updates elements for access rights 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$managementGroup = $helper->addAccessGroup('Supplierconnect - Manage Permissions');

$right = $helper->addAccessRight('Supplierconnect - Manage Permissions');

$element = $helper->addAccessElement('Epicor_Supplierconnect', 'Access_management', 'index', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Supplierconnect', 'Access_management', 'addgroup', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Supplierconnect', 'Access_management', 'editgroup', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Supplierconnect', 'Access_management', 'savegroup', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$helper->addAccessGroupRight($managementGroup->getId(), $right->getId());

$right = $helper->loadAccessRightByName('Supplierconnect - Dashboard - Access Page');
$helper->addAccessRightElement($right->getId(),'Epicor_Common','Account','index','','Access');

$installer->endSetup();