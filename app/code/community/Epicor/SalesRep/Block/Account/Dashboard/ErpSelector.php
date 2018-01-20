<?php

class Epicor_SalesRep_Block_Account_Dashboard_ErpSelector extends Epicor_Supplierconnect_Block_Customer_Info
{

    protected $_erpAccounts;

    public function _construct()
    {
        parent::_construct();

        $this->setTitle($this->__('Account Selector'));
        $this->setTemplate('epicor/salesrep/account/dashboard/erp_selector.phtml');
        $this->setColumnCount(1);
        $this->setOnRight(false);
    }

    public function isMasquerading()
    {
        $helper = Mage::helper('epicor_salesrep');
        /* @var $helper Epicor_SalesRep_Helper */
        return $helper->isMasquerading();
    }
    
    public function isMasqueradeAccount($erpAccount)
    {
        $helper = Mage::helper('epicor_salesrep');
        /* @var $helper Epicor_SalesRep_Helper_Data */
        $currentErpAccount = $helper->getErpAccountInfo();

        return $currentErpAccount->getId() == $erpAccount->getId();
    }

    public function getErpAccounts($noChild=null)
    {
        if (!$this->_erpAccounts) {
            $account = Mage::registry('sales_rep_account');
            /* @var $account Epicor_SalesRep_Model_Account */
            $erpAccount = array();
            if($noChild) {
               $childStores = $account->getStoreMasqueradeAccountsNoChild();
            } else {
               $childStores = $account->getStoreMasqueradeAccounts(); 
            }            
            foreach ($childStores as $account) {
                $erpaccount[$account->getName() . $account->getId()] = $account;    // save account using name as a key 
            }
            ksort($erpaccount);                                                     // sort the erpaccount array by key
            foreach ($erpaccount as $erp) {
                $this->_erpAccounts[$erp->getEntityId()] =  $erp;                   // loop through the erpaccount list and use entity id as key to populate erpAccounts 
            }
        }
        return $this->_erpAccounts;
    }
    
    public function getCounts()
    {
        $account = Mage::registry('sales_rep_account');
        /* @var $account Epicor_SalesRep_Model_Account */
        return count($account->getErpAccountIds());
    }    
    

    public function getActionUrl()
    {
        return $this->getUrl('epicor_comm/masquerade/masquerade');
    }

    public function getReturnUrl()
    {
        $url = $this->getUrl('customer/account/index');
        return Mage::helper('epicor_comm')->urlEncode($url);
    }
    public function displaySearchButton()
    {    
        $display = false;
        if(count($this->_erpAccounts) >= Mage::getStoreConfig('epicor_salesrep/general/masquerade_search')){
            $display = true;
        }
        return $display;
    }

}
