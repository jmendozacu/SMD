<?php


class Epicor_Customerconnect_Block_Customer_Orders_Details_Billing extends Epicor_Customerconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $order = Mage::registry('customer_connect_order_details');
        $this->_addressData = $order->getOrderAddress();
        $this->setTitle($this->__('Sold To :'));
    }
}