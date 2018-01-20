<?php


class Epicor_Supplierconnect_Block_Customer_Invoices_Details_Address extends Epicor_Supplierconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $invoice = Mage::registry('supplier_connect_invoice_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */
        $this->_addressData = $invoice->getVarienDataFromPath('invoice/supplier_address');
        
        $this->setTitle($this->__('Supplier Information: '));
    }
}