<?php

class Epicor_Customerconnect_Block_Customer_Account_Billingaddress extends Epicor_Customerconnect_Block_Customer_Address
{

    public function __construct()
    {
        parent::__construct();
        $details = Mage::registry('customer_connect_account_details');

        if ($details) {
            $this->_addressData = $details->getInvoiceAddress();           

            $helper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */

            $this->setShowUpdateLink($helper->customerHasAccess('Epicor_Customerconnect', 'Account', 'saveBillingAddress', '', 'Access'));
            $this->setFormSaveUrl(Mage::getUrl('customerconnect/account/saveBillingAddress'));
        }
        $this->setTitle($this->__('Billing'));
        $this->setAddressType('billing');
    }

}
