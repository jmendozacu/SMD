<?php

/**
 * Version 1.0.0.28 to 1.0.0.29 upgrade
 * 
 * Set ERP Accounts with type "Both" to "Customer" (so it doesnt cause issues)
 */

$installer = $this;
$installer->startSetup();

$collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
/* @var $storeCollection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */
$collection->addFieldToFilter('account_type', 'Both');
foreach ($collection->getItems() as $erpAccount) {
    $erpAccount->setAccountType('Customer');
    $erpAccount->save();
}

$installer->endSetup();