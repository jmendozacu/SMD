<?php


class Epicor_Customerconnect_Block_Customer_Shipments_Details_Shipping extends Epicor_Customerconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $shipments = Mage::registry('customer_connect_shipments_details');  
        $this->_addressData = $shipments->getDeliveryAddress();       
        $this->setTitle($this->__('Ship To :')); 
        $this->setOnRight(true);
    }
}