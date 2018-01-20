<?php

/**
 * Version 1.0.0.2 to 1.0.0.3 upgrade
 * 
 * Adds excluded elements for access rights 
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('epicor_common/access_element');

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$element = $helper->addAccessElement('Epicor_B2b','Customer_account','login','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','loginPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','logout','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','logoutSuccess','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','create','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','createPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','confirm','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','confirmation','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','forgotPassword','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','forgotPasswordPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','resetPassword','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Customer_account','resetPasswordPost','','Access',1);
$helper->removeElementFromRights($element->getId());

$helper->regenerateModuleElements('Epicor_B2b');

$installer->endSetup();

