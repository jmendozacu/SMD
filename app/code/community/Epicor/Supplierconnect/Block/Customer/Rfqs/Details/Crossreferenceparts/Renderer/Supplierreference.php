<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Supplierreference extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {
            $html = '<input type="text" class="cross_reference_part_supplier_reference" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][supplier_reference]" value="' . $row->getSupplierReference() . '" />';
        } else {
            $html = $row->getSupplierReference();
        }
        return $html;
    }

}
