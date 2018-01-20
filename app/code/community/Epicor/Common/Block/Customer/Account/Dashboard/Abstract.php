<?php

/**
 * Customer Account Dashboard Block, used for back links
 * 
 * @author gareth.james
 */
class Epicor_Common_Block_Customer_Account_Dashboard_Abstract extends Mage_Customer_Block_Account_Dashboard
{
    protected $_defaultUrl;
    
    /**
     * Get back url in account dashboard
     *
     * This method is copypasted in:
     * Mage_Wishlist_Block_Customer_Wishlist  - because of strange inheritance
     * Mage_Customer_Block_Address_Book - because of secure url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getUrl($this->_defaultUrl);
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            
            if($this->getRequest()->getParam('back')) {
                $url = Mage::helper('epicor_common')->urlDecode($this->getRequest()->getParam('back'));
            } else {
                $url = $this->getRefererUrl();
            }
        }
        
        return $url;
    }
    
    /**
     * Get list url in account dashboard
     *
     * @return string
     */
    public function getListPageUrl()
    {
        $value = $this->getListUrl();
        
        if($this->getRequest()->getParam('list_url')) {
            $value = Mage::helper('epicor_common')->urlDecode($this->getRequest()->getParam('list_url'));
        }
        
        return $value;
    }
    
    /**
     * Get list url in account dashboard
     *
     * @return string
     */
    public function getListTypeVal()
    {
        $value = $this->getListType();
        
        if($this->getRequest()->getParam('list_type')) {
            $value = $this->getRequest()->getParam('list_type');
        }
        
        return $value;
    }
}