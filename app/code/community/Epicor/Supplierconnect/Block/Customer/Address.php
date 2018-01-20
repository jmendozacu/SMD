<?php
/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method void setOnRight(bool $bool)
 * @method bool getOnRight()
 */
class Epicor_Supplierconnect_Block_Customer_Address extends Mage_Core_Block_Template {
    /**
     *  @var Varien_Object 
     */
    protected $_addressData;
    protected $_countryCode;
    
    public function _construct() {
        parent::_construct();
        $this->_addressData = new Varien_Object();
        $this->setTemplate('supplierconnect/address.phtml');
        $this->setOnRight(false);
    }
    
    public function getName() {
        return $this->_addressData->getName();
    }
    
    public function getStreet() {
        $street = $this->_addressData->getAddress1();
        $street .= $this->_addressData->getAddress2() ? ', '.$this->_addressData->getAddress2() : '';
        $street .= $this->_addressData->getAddress3() ? ', '.$this->_addressData->getAddress3() : '';
        return $street;
    }
    
    public function getCity() {
        return $this->_addressData->getCity();
    }
    
    public function getCounty() {
        $helper = Mage::helper('supplierconnect');
        /* @var $helper Epicor_Supplierconnect_Helper_Data */
        $region = $helper->getRegionFromCountyName($this->getCountryCode(), $this->_addressData->getCounty());

        return ($region) ? $region->getName() : $this->_addressData->getCounty();
    }
    
    public function getPostcode() {
        return $this->_addressData->getPostcode();
    }
    
    public function getCountryCode() {
        
        if (is_null($this->_countryCode)) {
            $helper = Mage::helper('supplierconnect');
            /* @var $helper Epicor_Supplierconnect_Helper_Data */
            $this->_countryCode = $helper->getCountryCodeForDisplay($this->_addressData->getCountry(), $helper::ERP_TO_MAGENTO);
        }
        
        return $this->_countryCode;
    }
    
    public function getCountry() {
        try {
            $helper = Mage::helper('supplierconnect');
            /* @var $helper Epicor_Supplierconnect_Helper_Data */

            return $helper->getCountryName($this->getCountryCode());
        } catch (Exception $e) {
            return $this->_addressData->getCountry();
        }
        
    }
    
    public function getTelephoneNumber() {
        return $this->_addressData->getTelephoneNumber();
    }
    
    public function getFaxNumber() {
        return $this->_addressData->getFaxNumber();
    }
}