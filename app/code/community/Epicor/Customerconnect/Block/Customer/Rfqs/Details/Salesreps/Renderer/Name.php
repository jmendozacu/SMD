<?php

/**
 * 
 * RFQ Sales rep editable text field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Salesreps_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);

        $editable = false;

        if ($editable && Mage::registry('rfqs_editable')) {
            $html = '<input type="text" name="salesreps[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="salesreps_' . $index . '"/>';
        } else {
            $html = $value;
            $html .= '<input type="hidden" name="salesreps[' . $key . '][' . $row->getUniqueId() . '][' . $index . ']" value="' . $value . '" class="salesreps_' . $index . '"/>';
        }

        return $html;
    }

}
