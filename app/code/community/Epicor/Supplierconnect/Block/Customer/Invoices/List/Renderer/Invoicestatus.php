<?php

/**
 * Invoice status "Open" display, shows Yes / No based on status value
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Invoices_List_Renderer_Invoicestatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $helper = Mage::helper('customerconnect/messaging');

        $index = $this->getColumn()->getIndex();
        return $helper->getInvoiceStatusDescription($row->getData($index));
    }

}
