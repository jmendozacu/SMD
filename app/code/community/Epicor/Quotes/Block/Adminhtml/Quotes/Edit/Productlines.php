<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Productlines extends Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Abstract
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
    
    public function getUpdateTotalsUrl() {
        return Mage::helper("adminhtml")->getUrl("adminhtml/quotes_quotes/updatetotals/", array('id' => $this->getQuote()->getId()));
    }
    
    public function getAcceptUrl() {
        return Mage::helper("adminhtml")->getUrl("adminhtml/quotes_quotes/accept/", array('id' => $this->getQuote()->getId()));
    }

}
