<?php

/**
 * Contract Settings Default Configurations
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Contract_Default extends Mage_Core_Block_Template
{


    /**
     *
     * @var Epicor_Comm_Model_Customer_Erpaccount 
     */
    private $_erpAccount;	


    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Default Contract'));
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
        return Mage::getUrl('epicor_lists/contract/save');
    }    


    public function getReturnUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        return Mage::helper('epicor_lists')->urlEncode($url);
    }   


    public function getAjaxAddressUrl()
    {
        return Mage::getUrl('lists/contract/getcontractaddress/');
    }  


    /**
     * Get session customer allowed contracts
     * 
     * @return array
     */
    public function getCustomerAllowedContracts()
    {
        $contracts = $this->getContractHelper()->getActiveContracts();
        $customerData = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        $defaultContract = $customerData->getEccDefaultContract();
        if (!is_array($contracts)) {
            $select = array();
        } else {
            $select  = '<select name="contract_default" id="contract_default" class="select absolute-advice">';
            $select .= '<option value="">No Default Contract</option>';
            foreach ($contracts as $code => $contractvals) {
                $defaultSelect  = ($code == $defaultContract ? "selected=selected" : ""); 
                $select .= '<option value="' . $code . '" '.$defaultSelect.'>' . $contractvals->getTitle() . '</option>';
            }
            $select .= '</select>';
        
        }
        return $select;
    }  



    /**
     * Get Selected Customer Address for the particular Contract
     * 
     * @return array
     */
    public function getCustomerSelectedAddress($contractId)
    {
        $loadHelper = Mage::helper('epicor_lists/frontend')->customerAddresses($contractId);
        $customerData = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        $defaultContractAddress = $customerData->getEccDefaultContractAddress();
        $select['type'] = 'success';
        $select['html'] = '<select name="contract_default_address" id="contract_default_address" class="select absolute-advice">';
            $select['html'] .= '<option value="">No Default Address</option>';
            foreach ($loadHelper as $code => $address) {
                $defaultSelect  = ($code == $defaultContractAddress ? "selected=selected" : ""); 
                $select['html'] .= '<option value="' . $code . '" '.$defaultSelect.'>' . $address->getName() . '</option>';
            }
            $select['html'] .= '</select>';
        return $select;
    }   


}
