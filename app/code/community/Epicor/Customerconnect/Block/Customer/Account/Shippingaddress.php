<?php

class Epicor_Customerconnect_Block_Customer_Account_Shippingaddress extends Epicor_Customerconnect_Block_Customer_Address
{

    public function __construct()
    {
        parent::__construct();
        $details = Mage::registry('customer_connect_account_details');
        if ($details) {
            $this->_addressData = $details->getInvoiceAddress();
            $this->setFormSaveUrl(Mage::getUrl('customerconnect/account/saveShippingAddress'));
        }
        $this->setTitle($this->__('Shipping'));
        $this->setAddressType('shipping');
    }

}
