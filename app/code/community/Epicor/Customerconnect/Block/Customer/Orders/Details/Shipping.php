<?php


class Epicor_Customerconnect_Block_Customer_Orders_Details_Shipping extends Epicor_Customerconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $order = Mage::registry('customer_connect_order_details');
        $this->_addressData = $order->getDeliveryAddress();
        $this->setTitle($this->__('Ship To :'));
        $this->setOnRight(true);
    }
}