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

$managementGroup = $helper->addAccessGroup('Customerconnect - Manage Permissions');

$right = $helper->addAccessRight('Customerconnect - Manage Permissions');

$element = $helper->addAccessElement('Epicor_Customerconnect', 'Account', 'index', 'manage_permissions', 'view', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Customerconnect', 'Access_management', 'index', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Customerconnect', 'Access_management', 'addgroup', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Customerconnect', 'Access_management', 'editgroup', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$element = $helper->addAccessElement('Epicor_Customerconnect', 'Access_management', 'savegroup', '', 'Access', 0);
$helper->removeElementFromRights($element->getId());
$helper->addAccessRightElementById($right->getId(), $element->getId());

$helper->addAccessGroupRight($managementGroup->getId(), $right->getId());

$installer->endSetup();