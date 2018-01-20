<?php

/**
 * RFQ line currency column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $rfq = Mage::registry('customer_connect_rfq_details');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $index = $this->getColumn()->getIndex();
        $currency = $helper->getCurrencyMapping($rfq->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
        $price = $row->getData($index);
        $html = '<input type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $row->getData($index) . '" class="lines_' . $index . '"/>';
        if ($price == 'TBC' || $price == '') {
            $html .= '<span class="lines_' . $index . '_display">' . $price . '</span>';
        } else {
            $html .= '<span class="lines_' . $index . '_display">' . $helper->formatPrice($price, true, $currency) . '</span>';
        }

        return $html;
    }

}
