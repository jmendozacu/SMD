<?php

class Epicor_SalesRep_Block_Checkout_Onepage_Salesrep_Contact extends Mage_Checkout_Block_Onepage_Abstract
{

    protected function _construct()
    {
        $this->getCheckout()->setStepData('salesrep_contact', array(
            'label' => Mage::helper('checkout')->__('Choose Recipient ERP Account Contact'),
            'is_show' => $this->isShow(),
            'allow' => 'allow'
        ));

        if ($this->isShow()) {
            $billing = $this->getCheckout()->getStepData('billing');

            if (isset($billing['allow'])) {
                unset($billing['allow']);
            }

            $this->getCheckout()->setStepData('billing', $billing);
        }

        parent::_construct();
    }

    public function isShow()
    {
        $helper = Mage::helper('epicor_salesrep/checkout');
        /* @var $helper Epicor_Salesrep_Helper_Checkout */

        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */

        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */
        
        return $helper->isChooseContactEnabled() && $customer->isSalesRep() && $this->getContacts();
    }

    public function isRequired()
    {
        $helper = Mage::helper('epicor_salesrep/checkout');
        /* @var $helper Epicor_Salesrep_Helper_Checkout */

        return $helper->isChooseContactRequired();
    }

    public function getContacts()
    {
        $helper = Mage::helper('epicor_salesrep/checkout');
        /* @var $helper Epicor_Salesrep_Helper_Checkout */
        
        return $helper->getSalesErpContacts();
    }

    public function getCurrentContact()
    {
        return $this->getQuote()->getEccSalesrepChosenCustomerId();
    }

}
