<?php

/**
 * Version 1.0.0.1 to 1.0.0.2 upgrade
 * 
 * Adds excluded elements for access rights 
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('epicor_common/access_element');

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$element = $helper->addAccessElement('Epicor_B2b','Account','login','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','loginPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','logout','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','logoutSuccess','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','create','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','createPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','confirm','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','confirmation','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','forgotPassword','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','forgotPasswordPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','resetPassword','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Account','resetPasswordPost','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Portal','login','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Portal','error','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Portal','register','','Access',1);
$helper->removeElementFromRights($element->getId());
$element = $helper->addAccessElement('Epicor_B2b','Portal','registerPost','','Access',1);
$helper->removeElementFromRights($element->getId());



$helper->regenerateModuleElements('Epicor_B2b');

$installer->endSetup();

