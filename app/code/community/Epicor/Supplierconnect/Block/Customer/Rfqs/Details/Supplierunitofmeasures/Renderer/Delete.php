<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '<input type="checkbox" class="suom_delete" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][delete]" />';
        return $html;
    }

}
