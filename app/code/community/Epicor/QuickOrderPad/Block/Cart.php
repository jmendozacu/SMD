<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Epicor_QuickOrderPad_Block_Cart extends Mage_Checkout_Block_Cart {

    public function __construct() {
        parent::__construct();
        $quote = $this->getQuote();
        if (!$quote->validateMinimumAmount()) {
            $amount = Mage::helper('epicor_comm')->getMinimumOrderAmount($quote->getCustomer()->getErpaccountId());
            $_fromCurr = $quote->getBaseCurrencyCode();
            $_toCurr = Mage::app()->getStore()->getCurrentCurrencyCode();
            $minimumAmount = Mage::helper('epicor_comm')->getCurrencyConvertedAmount($amount, $_fromCurr, $_toCurr);
            $warning = Mage::getStoreConfig('sales/minimum_order/description') ? Mage::getStoreConfig('sales/minimum_order/description') : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);
            $this->setWarning($warning);
        }
    }
}


