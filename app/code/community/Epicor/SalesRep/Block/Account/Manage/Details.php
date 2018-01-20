<?php

class Epicor_SalesRep_Block_Account_Manage_Details extends Epicor_SalesRep_Block_Account_Manage_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor/salesrep/account/manage/details.phtml');
        $this->setTitle($this->__('Details'));
    }

    public function getSalesRepAccount()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        
        return $helper->getManagedSalesRepAccount();
    }

    public function canEdit()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        
        return $helper->canEdit();
    }

}
