<?php

/**
 *  Contract Filter block for lists
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Contract_Filter extends Mage_Core_Block_Template
{


    /**
     *
     * @var Epicor_Comm_Model_Customer_Erpaccount 
     */
    private $_erpAccount;	
    protected $_displayDefaultContractFilters;    


    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Contract Filter'));
    }    

    /**
     * Get Contract Helper
     * @return Epicor_Lists_Helper_Frontend_Contract
     */
    public function getContractHelper()
    {
        if (!$this->_contractHelper) {
            $this->_contractHelper = Mage::helper('epicor_lists/frontend_contract');
        }
        return $this->_contractHelper;
    }



    public function getActualAccount()
    {
        $commHelper = Mage::helper('epicor_comm');
        if (is_null($this->_erpAccount)) {
            $this->_erpAccount = $commHelper->getErpAccountInfo(null, 'customer', null, false);
        }
        return $this->_erpAccount;
    }    


    public function isAllowed()
    {
        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        $customer = $customerSession->getCustomer();
        $allowed = false;
        if ($customer->getId()) {
                $allowed = true;
        }
        return $allowed;
    }


    public function getFormUrl()
    {
        return Mage::getUrl('epicor_lists/contract/filter');
    }    


    /**
     * Checks config to see if user can choose single or multiple contracts
     * 
     * @return boolean
     */
    public function canChooseMultipleContracts()
    {
        return true;
    }   


    public function getReturnUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        return Mage::helper('epicor_lists')->urlEncode($url);
    }   


    /**
     * Get session customer allowed contracts
     * 
     * @return array
     */
    public function getCustomerAllowedContracts()
    {
        $contracts = $this->getContractHelper()->getActiveContracts();
        if (!is_array($contracts)) {
            $contracts = array();
        }
        return $contracts;
    }    
    
    
    public function getSelectedFilterContracts()
    {
        $customerSession = Mage::getSingleton('customer/session')->getCustomer();
        $eccContractsFilter = $customerSession->getEccContractsFilter();        
        return $eccContractsFilter;
    }
    
    /**
     * 
     * @param string $code
     * 
     * @return boolean
     */
    public function isDefaultFilterSelected($code)
    {
        return in_array($code, $this->getCustomerDefaultFilterCodes());
    }   
    
    
    public function getCustomerDefaultFilterCodes($codes = false)
    {
        $session = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $session Mage_Customer_Model_Session */
            if ($session->getEccContractsFilter()) {
                $_displayDefaultContractFilters = $session->getEccContractsFilter();
            }
        return $codes ? array_keys($_displayDefaultContractFilters) : $_displayDefaultContractFilters;
    }    

  

}
