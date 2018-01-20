<?php

/**
 * Version 1.0.0.49 to 1.0.0.50 upgrade erp account codes to include the company code
 * 
 */
$erp_accounts = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();

foreach ($erp_accounts->getItems() as $erp_customer) {
    $data = unserialize($erp_customer->getBrands());
    $company = null;
    while (is_array($data) && !array_key_exists('company', $data) && array_key_exists(0, $data)) {
        $data = $data[0];
    }
    if (is_array($data) && array_key_exists('company', $data))
        $company = $data['company'];

    if (empty($company))
        $company = Mage::helper('epicor_comm')->getStoreBranding(Mage::app()->getDefaultStoreView()->getId())->getCompany();

    $erp_code = $erp_customer->getErpCode();
    $separator = Mage::helper('epicor_comm')->getUOMSeparator();
    if (!empty($company) && strpos($erp_code, $separator) === false) {
        $new_erp_code = $company . $separator . $erp_code;
        $erp_customer->setErpCode($new_erp_code)->save();
    }
}
