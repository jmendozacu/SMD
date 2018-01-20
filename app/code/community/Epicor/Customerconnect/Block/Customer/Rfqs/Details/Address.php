<?php

/**
 * 
 * RFQ address block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Address extends Epicor_Customerconnect_Block_Customer_Address
{

    public function _construct()
    {
        parent::_construct();
        $order = Mage::registry('customer_connect_rfq_details');
        $this->setTemplate('customerconnect/customer/account/rfqs/details/address.phtml');

        if (Mage::registry('rfq_new')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            $addresses = $customer->getAddressesByType('registered');

            if (!empty($addresses)) {
                $address = array_pop($addresses);
            } else {
                $address = $customer->getDefaultBillingAddress();
            }

            $this->setAddressFromCustomerAddress($address);
            $this->setShowName(false);
        } else {
            $this->setShowName(true);
            $this->_addressData = $order->getQuoteAddress();
        }
        $this->setTitle($this->__('Sold To :'));
        $this->setAddressType('quote');
        $this->setShowUpdateLink(true);
    }

    public function isEditable()
    {
        return $this->getAddressType() == 'quote' ? false : Mage::registry('rfqs_editable');
    }

}
