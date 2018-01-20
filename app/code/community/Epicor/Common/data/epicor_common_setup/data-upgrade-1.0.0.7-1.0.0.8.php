<?php

/**
 * Version 1.0.0.7 to 1.0.0.8 upgrade
 * 
 * Adding Elements Access Rights if installed
 * 
 */
$installer = $this;
$installer->startSetup();

if (Mage::helper('epicor_common')->isModuleEnabled('Epicor_Elements')) {
    $helper = Mage::helper('epicor_common/setup');
    /* @var $helper Epicor_Common_Helper_Setup */

    $helper->regenerateModuleElements('Epicor_Elements');

    $right = $helper->loadAccessRightByName('Customer - Full Access');
    /* @var $model Epicor_Common_Model_Access_Right */

    $element = $helper->addAccessElement('Epicor_Elements', 'Payment', 'bankredirect', '', 'Access', 0);
    $helper->addAccessRightElementById($right->getId(), $element->getId());

    $element = $helper->addAccessElement('Epicor_Elements', 'Payment', 'setupreturnAction', '', 'Access', 0);
    $helper->addAccessRightElementById($right->getId(), $element->getId());
}
$installer->endSetup();
