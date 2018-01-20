<?php

/**
 * Line reject checkbox display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_List_Renderer_Reject extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect/messaging');
        /* @var $helper Epicor_Customerconnect_Helper_Messaging */

        $status = $helper->getErpquoteStatusDescription($row->getQuoteStatus(), '', 'state');

        $disabled = '';

        if (!Mage::registry('rfqs_editable')
            || $status != Epicor_Customerconnect_Model_Config_Source_Quotestatus::QUOTE_STATUS_AWAITING
            || $row->getQuoteEntered() != 'Y'
        ) {
            $disabled = 'disabled="disabled"';
        }

        $html = '<input type="checkbox" name="rejected[]" value="' . $row->getId() . '" id="rfq_reject_' . $row->getId() . '" class="rfq_reject" ' . $disabled . '/>';

        return $html;
    }

}
