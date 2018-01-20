<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '<input type="checkbox" class="cross_reference_part_delete" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][delete]" />';
        return $html;
    }

}
