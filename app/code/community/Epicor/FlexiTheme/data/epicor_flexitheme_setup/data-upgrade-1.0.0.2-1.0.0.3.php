<?php
/**
 * Version 1.0.0.1 to 1.0.0.2_2 upgrade
 * 
 * Installs "Customer ERP Account Summary" block from Customerconnect  
 */

$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */
/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */

if($helper->isModuleEnabled('Epicor_Customerconnect')) {
    if(!$helper->findLayoutBlock('customerconnect/customer_account_summary')) {
        $helper->createLayoutBlock('Customer ERP Account Summary', 'customerconnect/customer_account_summary', 'customerconnect/customer/account/summary.phtml', NULL, NULL, NULL, NULL, 'customerconnect.customer.account.summary', NULL);
    }
}
