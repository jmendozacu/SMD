<?php

/**
 * Version upgrade-0.0.1.0-0.1.1 upgrade
 * 
 * 
 * Adds SKUs to access rights
 */
$installer = $this;
$installer->startSetup();


$crePaymentMapping = Mage::getModel('epicor_comm/erp_mapping_payment');
/* @var $esdmPaymentMapping Epicor_Comm_Model_Erp_Mapping_Payment */
$erp = $crePaymentMapping->loadMappingByStore('cre', 'magento_code');
if ($erp->getErpCode()) {
    $crePaymentMapping->delete();
}

$cardtype = Mage::getModel('epicor_comm/erp_mapping_cardtype')->getCollection()->addFieldToFilter('payment_method', array(
    'eq' => 'cre'));
foreach ($cardtype->getItems() as $cardTypes) {
   $cardTypes->delete(); 
}

$installer->endSetup();