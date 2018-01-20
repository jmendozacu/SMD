<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_New_Renderer_Confirm extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $disabled = (!Mage::registry('orders_editable')) ? 'disabled="disabled"' : '';
        
        $html = '<input type="checkbox" name="confirmed[]" value="' . $row->getId() . '" id="po_confirm_' . $row->getId() . '" class="po_confirm" '.$disabled.'/>'
                . '<input type="hidden" name="purchase_order[' . $row->getId() . '][order_date]" value="' . $row->getOrderDate() . '"/>'
                . '<input type="hidden" name="purchase_order[' . $row->getId() . '][order_status]" value="' . $row->getOrderStatus() . '"/>'
                . '<input type="hidden" name="purchase_order[' . $row->getId() . '][order_confirmed]" value="' . $row->getOrderConfirmed() . '"/>'
        ;

        return $html;
    }

}
