<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$salesRepAccessRights = array(
);

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->regenerateModuleElements('Epicor_SalesRep');

$salesRepGroup = $helper->addAccessGroup('ECC Default - Sales Rep Access');

$right = $helper->addAccessRight('ECC Default - Sales Rep - Full Access');
$helper->addAccessGroupRight($salesRepGroup->getId(), $right->getId());
$element = $helper->addAccessElement('Epicor_SalesRep', '*', '*', '', 'Access');
$helper->addAccessRightElementById($right->getId(), $element->getId());

$right = $helper->addAccessRight('ECC Default - Sales Rep - Masquerade Access');
$helper->addAccessGroupRight($salesRepGroup->getId(), $right->getId());
$element = $helper->addAccessElement('Epicor_Comm', 'Masquerade', '*', '', 'Access');
$helper->addAccessRightElementById($right->getId(), $element->getId());

$right = $helper->addAccessRight('ECC Default - Sales Rep - CustomerConnect Access');
$helper->addAccessGroupRight($salesRepGroup->getId(), $right->getId());
$element = $helper->addAccessElement('Epicor_Customerconnect', '*', '*', '', 'Access');
$helper->addAccessRightElementById($right->getId(), $element->getId());

$customerGroup = $helper->loadAccessGroupByName('Customer - Full Access');

$defaults = array($salesRepGroup->getId(), $customerGroup->getId());

$config = Mage::getConfig()->init();
/* @var $config Mage_Core_Model_Config */

$config->saveConfig('epicor_common/accessrights/salesrep_default', implode(',', $defaults));

Mage::app()->cleanCache(array('CONFIG'));