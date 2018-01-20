<?php
/**
 * Version 1.0.0.2 to 1.0.0.3 upgrade
 * 
 * Installs "Customer Account Summary from customer connect
 */

if(Mage::helper('customerconnect')->isModuleEnabled('Epicor_FlexiTheme')) {
    if(!Mage::helper('flexitheme/setup')->findLayoutBlock('customerconnect/customer_account_summary')) {
        Mage::helper('flexitheme/setup')->createLayoutBlock('Customer ERP Account Summary', 'customerconnect/customer_account_summary', 'customerconnect/customer/account/summary.phtml', NULL, NULL, NULL, NULL, 'customerconnect.customer.account.summary', NULL);
    }
}
