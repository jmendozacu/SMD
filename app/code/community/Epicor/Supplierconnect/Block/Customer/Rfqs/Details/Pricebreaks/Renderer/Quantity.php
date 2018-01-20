<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Pricebreaks_Renderer_Quantity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {

            $oldDetails = array(
                'price_break_code' => $row->getPriceBreakCode(),
                'quantity' => $row->getQuantity(),
                'modifier' => $row->getModifier(),
                'effective_price' => $row->getEffectivePrice()
            );

            $html = '<input type="hidden" name="price_breaks[existing][' . $row->getQuantity() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" />';
            $html .= '<input type="hidden" name="price_breaks[existing][' . $row->getQuantity() . '][price_break_code]" value="' . $row->getPriceBreakCode() . '" />';
            $html .= '<input type="text" class="price_break_min_quantity" name="price_breaks[existing][' . $row->getQuantity() . '][quantity]" value="' . $row->getQuantity() . '" />';
        } else {
            $html = $row->getQuantity();
        }
        return $html;
    }

}
