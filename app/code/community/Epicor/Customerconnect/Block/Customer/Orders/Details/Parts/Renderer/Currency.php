<?php

/**
 * Currency display, converts a row value to currency display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $order = Mage::registry('customer_connect_order_details');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $index = $this->getColumn()->getIndex();
        $currency = $helper->getCurrencyMapping($order->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
        if ($row->getData($index)) {
            return $helper->formatPrice($row->getData($index), true, $currency);
        } else {
            return $row->getData($index);
        }
    }

}
