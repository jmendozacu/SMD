<?php

class Epicor_Quotes_Block_View_Productlines extends Epicor_Quotes_Block_View_Abstract
{

    public function getProductLines()
    {
        $productlines = Mage::getModel('quotes/quote_product')->getCollection();
        /* @var $productlines Mage_Core_Model_Mysql4_Collection_Abstract */

        $productlines->addFieldToFilter('quote_id', $this->getQuote()->getId());
        return $productlines;
    }

    public function formatPrice($price, $show_currency = true, $currency_code = null)
    {
        return $this->helper('quotes')->formatPrice($price, $show_currency, $currency_code);
    }

}
