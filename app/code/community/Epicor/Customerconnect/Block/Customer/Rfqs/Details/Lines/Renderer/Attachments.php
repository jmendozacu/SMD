<?php

/**
 * RFQ line attachments column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Attachments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $html = '';

        $attachments = ($row->getAttachments()) ? $row->getAttachments()->getasarrayAttachment() : array();

        if (Mage::registry('rfqs_editable')) {
            $colspan = 11;
        } else {
            $colspan = 10;
        }

        $html = '</td>'
            . '</tr>'
            . '<tr class="lines_row attachment" id="row-attachments-' . $row->getUniqueId() . '" style="display: none;">'
            . '<td colspan="' . $colspan . '" class="shipping-row">';

        if (Mage::registry('current_rfq_row')) {
            Mage::unregister('current_rfq_row');
        }

        Mage::register('current_rfq_row', $row);

        $block = $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_lines_attachments');
        /* @var $block Epicor_Customerconnect_Block_Customer_Rfqs_Details_Attachments */

        $html .= $block->toHtml();

        return $html;
    }

}
