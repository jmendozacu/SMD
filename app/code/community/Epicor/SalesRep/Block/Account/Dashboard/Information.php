<?php
/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method void setOnRight(bool $bool)
 * @method bool getOnRight()
 */
class Epicor_SalesRep_Block_Account_Dashboard_Information extends Mage_Core_Block_Template {
    /**
     *  @var Varien_Object 
     */
    protected $_customer;
    
    public function _construct() {
        parent::_construct();
        $this->setTitle($this->__('Sales Rep Information'));
        $this->setTemplate('epicor/salesrep/account/dashboard/information.phtml');
        
        $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $this->_customer Epicor_Comm_Model_Customer */
    }
    
    public function getName() {
        return $this->_customer->getName();
    }
    
    public function getFunction() {
        return $this->_customer->getFunction();
    }
    
    public function getEmail() {
        return $this->_customer->getEmail();
    }
    
    public function getTelephoneNumber() {
        return $this->_customer->getTelephoneNumber();
    }
    
    public function getFaxNumber() {
        return $this->_customer->getFaxNumber();
    }
}