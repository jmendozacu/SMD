<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Supplierproductcode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {
            $html = '<input type="text" class="cross_reference_part_supplier_product_code" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][supplier_product_code]" value="' . $row->getSupplierProductCode() . '"/>';
        } else {
            $html = $row->getSupplierProductCode();
        }
        return $html;
    }

}
