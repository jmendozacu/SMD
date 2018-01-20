<?php

class Epicor_Supplierconnect_Block_Customer_Invoices_Details_Info extends Epicor_Supplierconnect_Block_Customer_Info {

    public function _construct() {
        parent::_construct();


        $invoiceMsg = Mage::registry('supplier_connect_invoice_details');

        if ($invoiceMsg) {

            $invoice = $invoiceMsg->getInvoice();

            if ($invoice) {

                ;
                
                $this->_infoData = array(
                    $this->__('Invoice Date: ') => $invoice->getInvoiceDate() ? $this->getHelper()->getLocalDate($invoice->getInvoiceDate(), Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true) : $this->__('N/A'),
                    $this->__('Terms: ') => $invoice->getPaymentTerms(),
                    $this->__('Due Date: ') => $invoice->getDueDate() ? $this->getHelper()->getLocalDate($invoice->getDueDate(), Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true) : $this->__('N/A'),
                    $this->__('Status: ') => Mage::helper('customerconnect/messaging')->getInvoiceStatusDescription($invoice->getInvoiceStatus()),
                    $this->__('Ref Invoice: ') => $invoiceMsg->getInvoiceNumber(),
                    $this->__('Discount Date: ') => $invoice->getDiscountDate() ? $this->getHelper()->getLocalDate($invoice->getDiscountDate(), Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true) : $this->__('N/A'),
                    $this->__('Currency: ') => $invoice->getCurrencyCode(),
                    
                );
            }
        }

        $this->setTitle($this->__('Invoice Information'));
    }

}