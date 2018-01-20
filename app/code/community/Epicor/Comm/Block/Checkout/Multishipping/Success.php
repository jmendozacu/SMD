<?php

class Epicor_Comm_Block_Checkout_Multishipping_Success extends Mage_Checkout_Block_Multishipping_Success {
    
    protected $_orders = array();
    
    public function __construct(){
        parent::__construct();
        $this->setTemplate('epicor_comm/checkout/multishipping/success.phtml');
    }
    
    public function getErpOrderNumber($orderId){
        if(!isset($this->_orders[$orderId])){
            $order = Mage::getModel('sales/order')->load($orderId);
            $this->_orders[$orderId] = $order->getErpOrderNumber();
        }
        
        return $this->_orders[$orderId];
    }
}
