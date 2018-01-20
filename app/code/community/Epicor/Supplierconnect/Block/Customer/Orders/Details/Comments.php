<?php

class Epicor_Supplierconnect_Block_Customer_Orders_Details_Comments extends Epicor_Supplierconnect_Block_Customer_Info {

    public function _construct() {
        parent::_construct();


        $orderMsg = Mage::registry('supplier_connect_order_details');

        if ($orderMsg) {

            $order = $orderMsg->getPurchaseOrder();

            if ($order) {

                $orderDisplay = Mage::registry('supplier_connect_order_display');
                
                if ($orderDisplay == 'edit') {
                    $comment = '<textarea id="supplier_connect_order_comments" name="purchase_order[comment]" cols="165" rows="4">' . $order->getComment() . '</textarea>';
                } else {
                    $comment = $order->getComment();
                }

                $this->_infoData = array();
                
                $this->_extraData = array(
                     $this->__('') => $comment
                );
            }
        }

        $this->setTitle($this->__('Comments'));
        $this->setColumnCount(1);
    }

}