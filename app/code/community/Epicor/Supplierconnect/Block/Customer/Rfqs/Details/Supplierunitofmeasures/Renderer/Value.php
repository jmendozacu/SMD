<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Value extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {

            $type = Mage::registry('allow_conversion_override') ? 'text' : 'hidden';

            $html = '<input type="' . $type . '" class="suom_value" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][value]" value="' . htmlspecialchars($row->getValue()) . '" /> ';

            if (!Mage::registry('allow_conversion_override')) {
                $html .= $row->getValue();
            }
        } else {
            $html = $row->getValue();
        }

        return $html;
    }

}
