<?php

/**
 * RFQ delivery address block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Delivery extends Epicor_Customerconnect_Block_Customer_Address
{

    public function _construct()
    {
        parent::_construct();
        $order = Mage::registry('customer_connect_rfq_details');
        $this->setTemplate('customerconnect/customer/account/rfqs/details/address.phtml');

        if (Mage::registry('rfq_new')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */

            if ($helper->isMasquerading()) {
                Mage::register('masq_address_hide_customer_name', true, true);
            }

            $this->setAddressFromCustomerAddress($customer->getPrimaryShippingAddress());
            $this->setShowName(false);
        } else {
            $this->_addressData = $order->getDeliveryAddress();
            $this->setShowName(true);
        }

        $this->setTitle($this->__('Ship To :'));
        $this->setOnRight(true);
        $this->setAddressType('delivery');
        $this->setShowUpdateLink(true);
    }

    public function isEditable()
    {
        return Mage::registry('rfqs_editable');
    }

}
