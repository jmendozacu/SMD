<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_New_Renderer_Reject extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $disabled = (!Mage::registry('orders_editable')) ? 'disabled="disabled"' : '';
        
        $html = '<input type="checkbox" name="rejected[]" value="' . $row->getId() . '" id="po_reject_' . $row->getId() . '" class="po_reject" '.$disabled.'/>';

        return $html;
    }

}
