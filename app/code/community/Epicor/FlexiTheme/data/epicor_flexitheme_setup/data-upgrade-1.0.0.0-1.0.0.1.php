<?php
/**
 * Version 1.0.0.0 to 1.0.0.1 upgrade
 * 
 * Installs "Customer ERP Account Summary" block
 */

$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */
/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */

// Retired will cause install issues if left in

//if($helper->isModuleEnabled('Epicor_B2b')) {
//    if(!$helper->findLayoutBlock('b2b/customer_account_summary')) {
//        $helper->createLayoutBlock('Customer ERP Account Summary', 'b2b/customer_account_summary', 'b2b/customer/account/summary.phtml', NULL, NULL, NULL, NULL, 'b2b.customer.account.summary', NULL);
//    }
//}
