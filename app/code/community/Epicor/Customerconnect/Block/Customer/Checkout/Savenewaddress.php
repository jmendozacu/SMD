<?php

/**
 * Customer Orders list
 */
class Epicor_Customerconnect_Block_Customer_Checkout_Savenewaddress extends Epicor_Customerconnect_Block_Customer_Info {

     public function __construct() {
        parent::__construct();
        if(Mage::helper('epicor_common')->customerAddressPermissionCheck('create')){ 
            $this->setTemplate('customerconnect/customer/checkout/savenewaddress.phtml');
            $values = Mage::helper('customerconnect')->getSaveBillingAddressErpValues();
            $this->setErpDropdownValues($values['erp_dropdown_values']);
            $this->setErpCurrentDropdownValue($values['erp_current_dropdown_value']);
            $this->setSaveBillingAddressValues($values['save_billing_address_values']);
            $this->setSaveBillingAddressCurrentValue($values['save_billing_address_current_value']);
        }    
     }   
}