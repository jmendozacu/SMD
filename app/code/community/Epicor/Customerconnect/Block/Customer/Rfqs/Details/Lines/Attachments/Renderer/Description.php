<?php

/**
 * RFQ line attachments editable text field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $line = Mage::registry('current_rfq_row');
        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);

        if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
            $html = '<input type="text" name="lineattachments[' . $key . '][' . $line->getUniqueId() . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="line_attachments_' . $index . '"/>';
        } else {
            $html = $value;
        }

        return $html;
    }

}
