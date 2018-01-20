<?php

class Epicor_Comm_Block_Checkout_Onepage_Success extends Mage_Checkout_Block_Onepage_Success {
    
    public function __construct(){
        parent::__construct();
        $this->setTemplate('epicor_comm/checkout/success.phtml');
    }
    
    protected function _prepareLastOrder()
    {
        parent::_prepareLastOrder();
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            /* @var $order Epicor_Comm_Model_Order */
            if ($order->getId()) {
                $this->addData(array(
                    'erp_order_number' => $order->getErpOrderNumber(),
                ));
            }
        }
    }

    public function getErpOrderNumber(){
        return $this->_getData('erp_order_number');
    }
}
