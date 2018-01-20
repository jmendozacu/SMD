<?php


class Epicor_Comm_Block_Checkout_Agreements extends Mage_Checkout_Block_Agreements
{
    /**
     * Override block template
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setTemplate('epicor_comm/checkout/agreements.phtml');
        return parent::_toHtml();
    }
    
    public function getMaxCommentSize()
    {
        if ($this->limitTextArea())
        {
             return  Mage::getStoreConfig('checkout/options/max_comment_length');
        }
        return '';
    }
    
    public function limitTextArea()
    {
        $result = false;
        if ($this->isCommentAllowed() && 
            Mage::getStoreConfigFlag('checkout/options/limit_comment_length')) {
             $value = Mage::getStoreConfig('checkout/options/max_comment_length');
             if (is_numeric($value))
             {
                 $result = true;
             }
        }
        return $result;
    }
    
    public function getAddressInstructions()
    {
        $session = Mage::getSingleton('checkout/session');
        /* @var $session Mage_Checkout_Model_Session */       
        $addressId = $session->getQuote()->getShippingAddress()->getCustomerAddressId();
        $customerAddress = Mage::getModel('customer/address')->load($addressId);
        return $customerAddress->getInstructions();
       
    }
    
    public function getRemainingCommentSize()
    {
        $max = $this->getMaxCommentSize();
        $current = $this->getAddressInstructions();
        return $max - strlen($current);
    }
    public function isCommentAllowed()
    {
      return Mage::getStoreConfig('checkout/options/show_comments');   
    }
}