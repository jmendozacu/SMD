<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Supplierunitofmeasures_Renderer_Operator extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {
            if (!Mage::registry('allow_conversion_override')) {
                $html = '<input type="hidden" class="suom_operator" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][operator]" value="' . htmlspecialchars($row->getOperator()) . '" /> ';
                $html .= ($row->getOperator() == '*') ? 'Multiply' : 'Divide';
            } else {
                $html = '<select class="suom_operator" name="supplier_unit_of_measures[existing][' . $row->getUnitOfMeasure() . '][operator]">';
                $html .= '<option value="*" ' . (($row->getOperator() == '*') ? 'selected="selected"' : '') . '>Multiply</option>';
                $html .= '<option value="/" ' . (($row->getOperator() == '/') ? 'selected="selected"' : '') . '>Divide</option>';
                $html .= '</select>';
            }
        } else {
            $html = ($row->getOperator() == '*') ? 'Multiply' : 'Divide';
        }
        return $html;
    }

}
