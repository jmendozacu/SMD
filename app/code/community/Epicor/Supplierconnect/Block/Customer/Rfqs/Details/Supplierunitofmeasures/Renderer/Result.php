<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Result extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {

            $type = Mage::registry('allow_conversion_override') ? 'text' : 'hidden';

            $html = '<input type="' . $type . '" class="suom_result" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][result]" value="' . htmlspecialchars($row->getResult()) . '" /> ';

            if (!Mage::registry('allow_conversion_override')) {
                $html .= $row->getResult();
            }
        } else {
            $html = $row->getResult();
        }

        return $html;
    }

}
