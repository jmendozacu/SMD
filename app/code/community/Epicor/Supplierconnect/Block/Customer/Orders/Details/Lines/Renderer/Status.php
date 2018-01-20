<?php

/**
 * Currency display, converts a row value to currency display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        return Mage::helper('supplierconnect/messaging')->getErpOrderStatusDescription($row->getLineStatus());
        
    }

}
