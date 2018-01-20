<?php

class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Addproducts_Summary extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        $this->setData(Mage::registry('return_data'));
        $this->setData('products', Mage::registry('products'));
        $this->setTemplate('epicor_comm/sales/order/view/addproduct/summary.phtml');
    }

    public function formatPrice($price)
    {
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $rate = Mage::app()->getStore()->getBaseCurrency()->getRate(Mage::app()->getStore()->getCurrentCurrencyCode());
        $data = floatval($price) * $rate;
        $data = sprintf("%f", $data);
        return Mage::app()->getLocale()->currency($currency_code)->toCurrency($data);
    }

}