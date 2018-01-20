<?php

class Epicor_Supplierconnect_Block_Customer_Orders_Details_Info extends Epicor_Supplierconnect_Block_Customer_Info {

    public function _construct() {
        parent::_construct();


        $orderMsg = Mage::registry('supplier_connect_order_details');

        if ($orderMsg) {

            $order = $orderMsg->getPurchaseOrder();

            if ($order) {
                
                $this->_infoData = array(
                    $this->__('Order Date: ') => $order->getOrderDate() ? $this->getHelper()->getLocalDate($order->getOrderDate(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) : $this->__('N/A'),
                    $this->__('Order Status: ') =>  $this->getHelper()->getErpOrderStatusDescription($order->getOrderStatus()),
                    $this->__('Terms: ') => $order->getPaymentTerms(),
                    $this->__('Currency: ') => $order->getCurrencyCode(),
                    $this->__('FOB: ') => $order->getFob(),
                    
                    $this->__('Confirmation : ') => ($order->getOrderConfirmed() != '')?
                                                    ($order->getOrderConfirmed() == 'C')?
                                                            'Confirmed'
                                                                :
                                                            'Rejected'    
                                                    :
                                                    null
                );
            }
        }

        $this->setTitle($this->__('Order Information'));
    }
    
    public function _toHtml()
    {
        $orderMsg = base64_encode(serialize(Mage::registry('supplier_connect_order_details')));
        
        $html = '<input type="hidden" name="oldData" value="'.$orderMsg.'" />';
        $html .= parent::_toHtml();
        return $html;
    }

}