<?php

/**
 * Invoice status display, converts an erp invoice status code to mapped invoice status
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_Erporderstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $helper = Mage::helper('supplierconnect/messaging');

        $index = $this->getColumn()->getIndex();
        return $helper->getErporderStatusDescription($row->getData($index));
    }

}
