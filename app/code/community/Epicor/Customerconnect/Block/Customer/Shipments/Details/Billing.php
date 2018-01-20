<?php


class Epicor_Customerconnect_Block_Customer_Shipments_Details_Billing extends Epicor_Customerconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $billing = Mage::registry('customer_connect_shipments_details');        
        $this->_addressData = $billing->getOrderAddress();      
        $this->setTitle($this->__('Sold To :'));           
    }
    
}