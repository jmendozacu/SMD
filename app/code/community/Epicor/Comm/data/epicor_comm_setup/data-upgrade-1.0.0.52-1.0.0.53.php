<?php

/**
 * Version 1.0.0.52 to 1.0.0.53 upgrade erp account codes to include the company code & short code
 * 
 */
$erp_accounts = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();

foreach ($erp_accounts->getItems() as $erp_customer) {
    $erp_code = $erp_customer->getErpCode();
    $delimiter = Mage::helper('epicor_comm/messaging')->getUOMSeparator();
    $parts = explode($delimiter, $erp_code, 2);

    $company = $short_code = $account_number = null;
    if (count($parts) > 1) {
        $company = $parts[0];
        $short_code = $account_number = $parts[1];
    } else {
        $short_code = $account_number = $parts[0];
    }
    $erp_customer->setCompany($company);
    $erp_customer->setShortCode($short_code);
    $erp_customer->setAccountNumber($account_number);
    $erp_customer->save();
}
