<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Attachments_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);
        if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
            $html = '<input type="text" name="attachments[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="attachments_' . $index . '"/>';
        } else {
            $html = $value;
        }

        return $html;
    }

}
