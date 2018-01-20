<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_EffectivePrice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {
            $html = '<input type="hidden" class="price_break_effective_price" name="price_breaks[existing][' . $row->getQuantity() . '][effective_price]" value="' . $row->getEffectivePrice() . '" readonly="readonly" />';
        }

        $html .= '<span class="price_break_effective_price_label">' . $row->getEffectivePrice() . '</span>';

        return $html;
    }

}
