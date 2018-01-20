<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '<input type="checkbox" class="price_break_delete" name="price_breaks[existing][' . $row->getQuantity() . '][delete]" value="1"/>';
        return $html;
    }

}
