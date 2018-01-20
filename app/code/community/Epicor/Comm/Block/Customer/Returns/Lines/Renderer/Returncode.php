<?php

class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Returncode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */

        if (!Mage::registry('review_display') && $row->isActionAllowed('Return code')) {
            $disabled = $row->getToBeDeleted() == 'Y' ? ' disabled="disabled"' : '';
            $html = '<select name="lines[' . $row->getUniqueId() . '][return_code]" class="return_line_returncode validate-select"' . $disabled . '>';

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */
            $codes = $customer->getReturnReasonCodes();

            $html .= '<option value="">Please select</option>';
            
            foreach ($codes as $code => $description) {
                $selected = $row->getReasonCode() == $code ? 'selected="selected"' : '';
                $html .= '<option value="' . $code . '" ' . $selected . '>' . $description . '</option>';
            }
            $html .= '</select>';
        } else {
            $helper = Mage::helper('customerconnect/messaging');
            /* @var $helper Epicor_Customerconnect_Helper_Messaging */
            
            $html = $helper->getReasonCodeDescription($row->getReasonCode());
        }

        return $html;
    }

}
