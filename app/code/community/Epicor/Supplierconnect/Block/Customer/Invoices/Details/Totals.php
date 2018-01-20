<?php

class Epicor_Supplierconnect_Block_Customer_Invoices_Details_Totals extends Epicor_Common_Block_Generic_Totals {

    public function _construct() {
        parent::_construct();
        $invoiceMsg = Mage::registry('supplier_connect_invoice_details');

        $invoice = $invoiceMsg->getInvoice();

        if ($invoice) {
            $helper = Mage::helper('epicor_comm/messaging');

            $currencyCode = $helper->getCurrencyMapping($invoice->getCurrencyCode(), Epicor_Customerconnect_Helper_Data::ERP_TO_MAGENTO);

            $this->addRow('Line Charges :', $helper->getCurrencyConvertedAmount($invoice->getGoodsTotal(), $currencyCode), 'subtotal');
            $this->addRow('Invoice Amount :', $helper->getCurrencyConvertedAmount($invoice->getGrandTotal(), $currencyCode), 'shipping');
            $this->addRow('Balance Due :', $helper->getCurrencyConvertedAmount($invoice->getBalanceDue(), $currencyCode), 'grand_total');
        }

        $this->setColumns(9);
    }

}