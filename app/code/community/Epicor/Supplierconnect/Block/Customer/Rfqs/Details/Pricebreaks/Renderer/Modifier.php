<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Modifier extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {
            $html = '<input type="text" class="price_break_modifier" name="price_breaks[existing][' . $row->getQuantity() . '][modifier]" value="' . $row->getModifier() . '" />';
        } else {
            $html = $row->getModifier();
        }
        
        return $html;
    }

}
