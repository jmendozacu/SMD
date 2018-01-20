<?php
/**
 * Version 1.0.0.105 to 1.0.0.106 upgrade
 * 
 * Adds masquerade block to flexitheme
 */

$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_FlexiTheme_Helper_Setup */

if ($helper->isModuleEnabled('Epicor_Comm')) {   
    if (!$helper->findLayoutBlock('epicor_comm/customer_account_masquerade')) {
        $helper->createLayoutBlock('Account Masquerade Selector', 'epicor_comm/customer_account_masquerade', 'epicor_comm/customer/account/masquerade.phtml', NULL, NULL, NULL, NULL, 'epicor_comm.cart.masquerade', NULL);
    }
}
