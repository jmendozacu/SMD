<?php

/**
 * RFQ salesrep delete tickbox renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Salesreps_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        if (Mage::registry('rfqs_editable')) {
            $html = '<input type="checkbox" class="salesreps_delete" name="salesreps[' . $key . '][' . $row->getUniqueId() . '][delete]" />';
        } else {
            $html = '';
        }


        $oldDetails = array(
            'name' => $row->getName(),
            'number' => $row->getNumber(),
        );

        $html .= '<input type="hidden" name="salesreps[' . $key . '][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';

        return $html;
    }

}
