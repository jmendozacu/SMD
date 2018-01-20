<?php

class Epicor_Supplierconnect_Block_Customer_Orders_Details_Totals extends Epicor_Common_Block_Generic_Totals {

    public function _construct() {
        parent::_construct();
        $orderMsg = Mage::registry('supplier_connect_order_details');

        $order = $orderMsg->getPurchaseOrder();

        if ($order) {
            $helper = Mage::helper('epicor_comm/messaging');

            $currencyCode = $helper->getCurrencyMapping($order->getCurrencyCode(), Epicor_Customerconnect_Helper_Data::ERP_TO_MAGENTO);

            $this->addRow('Line(s) Subtotal :', $helper->getCurrencyConvertedAmount($order->getGoodsTotal(), $currencyCode), 'subtotal');
            $this->addRow('Total :', $helper->getCurrencyConvertedAmount($order->getGrandTotal(), $currencyCode), 'grand_total');
        }

        $this->setColumns(10);
    }

}