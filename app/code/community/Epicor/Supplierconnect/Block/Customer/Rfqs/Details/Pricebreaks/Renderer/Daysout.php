<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Daysout extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {
            $html = '<input type="text" class="price_break_days_out" name="price_breaks[existing][' . $row->getQuantity() . '][days_out]" value="' . $row->getDaysOut() . '" />';
        } else {
            $html = $row->getDaysOut();
        }

        return $html;
    }

}
