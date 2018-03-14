<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edit
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Customer_Address_Edit extends Mage_Customer_Block_Address_Edit {

    public function getMaxCommentSize() {
        if ($this->limitTextArea()) {
            return Mage::getStoreConfig('checkout/options/max_comment_length');
        }
        return '';
    }

    public function limitTextArea() {
        $result = false;
        if (Mage::getStoreConfigFlag('checkout/options/limit_comment_length')) {
            $value = Mage::getStoreConfig('checkout/options/max_comment_length');
            if (is_numeric($value)) {
                $result = true;
            }
        }
        return $result;
    }

    public function getRemainingCommentSize() {
        $max = $this->getMaxCommentSize();
        $current = $this->getAddress()->getInstructions();
        return $max - strlen($current);
    }

    public function canMarkDefaultShippingBillingAddress()
    {
        $helper = Mage::helper('epicor_comm/messaging_customer');
        /* @var $helper Epicor_Comm_Helper_Messaging_Customer */
        
        $storeId = Mage::app()->getStore()->getId();
        $cusDefaultAddressOverride = $helper->cusDefaultAddressOverride($storeId);
        
        if ($this->getCustomer()->isGuest() || $cusDefaultAddressOverride) {
            return true;
        }

        return false;
    }
    
    public function canSetAsDefaultBilling()
    {
        $helper = Mage::helper('epicor_comm/messaging_customer');
        /* @var $helper Epicor_Comm_Helper_Messaging_Customer */
        
        $storeId = Mage::app()->getStore()->getId();
        
        if ($this->getCustomer()->isGuest() && !$this->getCustomer()->getErpaccountId()) {
            return true;
        }

        return false;
    }
    
    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        
        $_return = !$this->isDefaultShipping();
        $helper = Mage::helper('epicor_comm/customer_address');
        /* @var $helper Epicor_Comm_Helper_Customer_Address */
        $type = $this->getCustomer()->isSupplier() ? 'supplier' : 'customer';
        $erpAccount = $helper->getErpAccountInfo(null, $type);
        if (!empty($erpAccount) && $this->getCustomer()->getErpaccountId()) {
            $address = $helper->getCustomerDefaultAddress($erpAccount, 'invoice', $this);
            $commonHelper = Mage::helper('epicor_common');
            /* @var $helper Epicor_Comm_Helper_Data */
            $_return = !$this->isDefaultShipping() && ($this->getAddress()->getErpAddressCode() != $address->getErpCode()) && $commonHelper->isDefaultAllowed($this->getAddress());
        }
        return $_return;
    }
    
    public function getSaveDefaultAddressUrl()
    {
        return Mage::getUrl('comm/customer_address/saveDefaultAddress', array('_secure'=>true, 'id'=>$this->getAddress()->getId()));
    }
}