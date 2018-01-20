<?php

/**
 * Version 1.0.0.5 to 1.0.0.6 upgrade
 * 
 * Adding coommon sales order to cusotmer full access rights
 * 
 */
$installer = $this;
$installer->startSetup();

if (Mage::helper('epicor_faqs')->isModuleEnabled('Epicor_Common')) {
    $helper = Mage::helper('epicor_common/setup');
    /* @var $helper Epicor_Common_Helper_Setup */

    $helper->regenerateModuleElements('Epicor_Faqs');

    $right = $helper->loadAccessRightByName('Customer - Full Access');
    /* @var $model Epicor_Common_Model_Access_Right */

    $element = $helper->addAccessElement('Epicor_Faqs', 'Index', '*', '', 'Access', 0);
    $helper->addAccessRightElementById($right->getId(), $element->getId());
    

}
$installer->endSetup();
