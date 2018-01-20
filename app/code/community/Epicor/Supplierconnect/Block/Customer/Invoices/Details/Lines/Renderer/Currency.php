<?php

/**
 * Currency display, converts a row value to currency display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Invoices_Details_Lines_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $invoice = Mage::registry('supplier_connect_invoice_details');
        
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $index = $this->getColumn()->getIndex();
        $currency = $helper->getCurrencyMapping($invoice->getInvoice()->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);

        return $helper->formatPrice($row->getData($index), true, $currency);
    }

}
