<?php

class Epicor_Customerconnect_Block_Customer_Dashboard_Registeredaddress extends Epicor_Customerconnect_Block_Customer_Address {

    public function __construct() {
        parent::__construct();
        
        if(Mage::registry('customerconnect_dashboard_ok')){
            $customer_helper = Mage::helper('epicor_comm/messaging_customer');
            /* @var $customer_helper Epicor_Comm_Helper_Messaging_Customer */

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Mage_Customer_Model_Customer */

            $addresses = $customer_helper->getErpAddresses($customer,'registered');

            $address = null;

            if(!empty($addresses)) {
                $address = array_pop($addresses);
            } else {
                $addresses = $customer_helper->getErpAddresses($customer,'invoice');
                if(!empty($addresses)) {
                    $address = array_pop($addresses);
                }
            }

            $this->setTitle($this->__('Registered Address'));
            $this->setAddressType('invoice');
            if(!empty($address)) {
                $this->_addressData = $address;
            }
        }
    }
}