<?php

/**
 * Sort out default group permissions fue to error in element generation process that was naming controllers in folders wrong
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->regenerateModuleElements('Epicor_Comm');

$right = $helper->loadAccessRightByName('Customer - Full Access');
/* @var $model Epicor_Common_Model_Access_Right */

$element = $helper->addAccessElement('Epicor_Comm', 'Configurator', 'editewa', '', 'Access', 0);
$helper->addAccessRightElementById($right->getId(), $element->getId());


$installer->endSetup();
