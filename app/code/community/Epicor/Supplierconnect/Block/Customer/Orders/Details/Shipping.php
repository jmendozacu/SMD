<?php


class Epicor_Supplierconnect_Block_Customer_Orders_Details_Shipping extends Epicor_Supplierconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $order = Mage::registry('supplier_connect_order_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */
        $this->_addressData = $order->getVarienDataFromPath('purchase_order/delivery_address');
        $this->setOnRight(true);
        $this->setTitle($this->__('Ship To: '));
    }
}