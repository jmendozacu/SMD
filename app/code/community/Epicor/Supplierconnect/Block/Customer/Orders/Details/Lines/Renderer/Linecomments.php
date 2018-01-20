<?php

/**
 * Line comment display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Linecomments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $index = $this->getColumn()->getIndex();
        $comment = $row->getData($index);

        $orderDisplay = Mage::registry('supplier_connect_order_display');
        
        if($orderDisplay == 'edit') {
            $html = '<textarea name="purchase_order[lines][' . $row->getId() . '][comment]">' . $comment . '</textarea>';
        } else {
            $html = $comment;
        }

        return $html;
    }

}
