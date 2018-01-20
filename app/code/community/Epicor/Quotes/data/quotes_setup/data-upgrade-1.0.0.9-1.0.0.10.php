<?php

/* 
 * Update created by on all quotes to fix issue where created by is not set on quote
 */

$installer = $this;
$installer->startSetup();

$quotes = Mage::getModel('quotes/quote')->getCollection();

foreach ($quotes as $quote) {
    /* @var $quote Epicor_Quotes_Model_quote */
    $customer = $quote->getCustomer(true);
    /* @var $customer Epicor_Comm_Model_Customer */
    $name = $customer->getName();
    $email = $customer->getEmail();

    $quote->setCreatedBy($name . ' (' . $email . ')');
    $quote->save();
}

$installer->endSetup();