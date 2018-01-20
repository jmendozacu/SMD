<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_ConversionFactor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '';
        
        if (Mage::registry('rfq_editable')) {
            $html .= '<input type="hidden" class="suom_conversion_factor" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][conversion_factor]" value="' . htmlspecialchars($row->getConversionFactor()) . '" /> ';
        }
        
        $html .= $row->getConversionFactor();

        return $html;
    }

}
