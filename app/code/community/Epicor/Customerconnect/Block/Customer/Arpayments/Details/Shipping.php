<?php
/**
 * AR Payments Payment
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Shipping extends Epicor_Customerconnect_Block_Customer_Address {

    public function _construct() {
        parent::_construct();
        $invoices = Mage::registry('customer_connect_invoices_details');  
        $this->_addressData = $invoices->getDeliveryAddress();       
        $this->setTitle($this->__('Ship To :')); 
        $this->setOnRight(true);
    }
}