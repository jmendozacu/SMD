<?php

/**
 * AR Payments Payment
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Billing extends Epicor_Customerconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $invoice = Mage::registry('customer_connect_invoices_details');        
        $this->_addressData = $invoice->getInvoiceAddress();      
        $this->setTitle($this->__('Sold To :'));       
    }
    
}