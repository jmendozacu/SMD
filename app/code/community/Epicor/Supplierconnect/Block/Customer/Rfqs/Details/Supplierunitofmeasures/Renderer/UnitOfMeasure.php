<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_UnitOfMeasure extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '';
        if (Mage::registry('rfq_editable')) {
            
            $oldDetails = array(
                'unit_of_measure' => $row->getUnitOfMeasure(),
                'conversion_factor' => $row->getConversionFactor(),
                'operator' => $row->getOperator(),
                'value' => $row->getValue(),
                'result' => $row->getResult()
            );
            
            $html = '<input type="hidden" class="suom_old_details" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '" /> ';
            $html .= '<input type="hidden" class="suom_unit_of_measure" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][unit_of_measure]" value="' . htmlspecialchars($row->getUnitOfMeasure()) . '" /> ';
        }
        
        $html .= $row->getUnitOfMeasure();

        return $html;
    }

}
