<?php

/**
 * Currency display, converts a row value to currency display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $order = Mage::registry('supplier_connect_order_details');
        
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $index = $this->getColumn()->getIndex();
        $currency = $helper->getCurrencyMapping($order->getPurchaseOrder()->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);

        return $helper->formatPrice($row->getData($index), true, $currency);
    }

}
