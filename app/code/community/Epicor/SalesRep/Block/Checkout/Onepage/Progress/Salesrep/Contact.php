<?php

class Epicor_SalesRep_Block_Checkout_Onepage_Progress_Salesrep_Contact extends Mage_Checkout_Block_Onepage_Progress
{

    private $_salesrepCustomer;

    public function getCurrentContact()
    {
        if (!$this->_salesrepCustomer) {
            $info = $this->getQuote()->getEccSalesrepChosenCustomerInfo();
            $useInfo = (!empty($info)) ? unserialize($info) : array('name' => 'N/A');
            $this->_salesrepCustomer = new Varien_Object($useInfo);
        }

        return $this->_salesrepCustomer;
    }

    public function getSalesrepCustomerName()
    {
        return $this->getCurrentContact()->getName();
    }

    public function getSalesrepCustomerEmail()
    {
        return $this->getCurrentContact()->getEmail();
    }

}
