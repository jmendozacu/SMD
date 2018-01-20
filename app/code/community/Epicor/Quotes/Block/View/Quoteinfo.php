<?php

class Epicor_Quotes_Block_View_Quoteinfo extends Epicor_Quotes_Block_View_Abstract
{

    public function getExpires()
    {
        return $this->helper('quotes')->getHumanExpires($this->getQuote());
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('epicor_quotes/manage/update', array('id' => $this->getQuote()->getId()));
    }
    public function getDuplicateUrl()
    {
        return $this->getUrl('epicor_quotes/manage/saveDuplicate', array('id' => $this->getQuote()->getId()));
    }
    public function getReSubmitUrl()
    {
        return $this->getUrl('epicor_quotes/manage/resubmit', array('id' => $this->getQuote()->getId()));
    }

    public function getUpdatedAt()
    {
        return $this->helper('quotes')->getLocalDate($this->getQuote()->getUpdatedAt());
    }

    public function allowGlobalTickbox()
    {
        $customerGlobal = Mage::getStoreConfigFlag('epicor_quotes/general/allow_customer_global');

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */#

        $allowGlobal = false;

        if ($customer->isCustomer() && $customerGlobal) {
            $allowGlobal = true;
        }

        return $allowGlobal;
    }

    public function showCreatedBy()
    {
        return $this->getQuote()->getIsGlobal();
    }

}