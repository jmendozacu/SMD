<?php

class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */

        if (!Mage::registry('review_display') && $row->isActionAllowed('Quantity')) {
            $disabled = $row->getToBeDeleted() == 'Y' ? ' disabled="disabled"' : '';
            $html = '<input type="text" name="lines[' . $row->getUniqueId() . '][quantity_returned]" value="' . round($row->getQtyReturned()) . '" class="return_line_quantity_returned"' . $disabled . '/>';
        } else {
            $html = round($row->getQtyReturned());
        }

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $source = $row->getSourceType();

        $html .= '<span class="return_line_quantity_ordered">';
        if ($source != 'sku') {
            $html .= ' / ';
            $html .= round($row->getQtyOrdered());
        }
        $html .= '</span>';

        return $html;
    }

}
