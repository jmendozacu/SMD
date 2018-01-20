<?php

/**
 * RFQ line editable text field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $index = $this->getColumn()->getIndex();
        if ($row->getData($index)) {
            $value = $row->getData($index) * 1;
        } else {
            $value = $row->getData($index);
        }
        if (Mage::registry('rfqs_editable')) {
            $html = '<input type="text" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="lines_' . $index . '"/>';
        } else {
            $html = $value;
            $html .= '<input type="hidden" name="lines[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="lines_' . $index . '"/>';
        }

        return $html;
    }

}
