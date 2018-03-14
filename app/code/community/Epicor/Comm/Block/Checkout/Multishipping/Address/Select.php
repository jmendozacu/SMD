<?php

/**
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Checkout_Multishipping_Address_Select extends Mage_Checkout_Block_Multishipping_Address_Select
{

    public function restrictAddressTypes()
    {
        return Mage::getStoreConfigFlag('Epicor_Comm/address/force_type');
    }

    public function getAddressCollection()
    {
        $collection = $this->getData('address_collection');
        if (is_null($collection)) {
            $collection = ($this->restrictAddressTypes()) ? $this->_getCheckout()->getCustomer()->getAddressesByType('invoice',true) : $this->_getCheckout()->getCustomer()->getAddresses();
            $this->setData('address_collection', $collection);
        }
        return $collection;
    }

    public function canAddNew()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->createBillingAddress();
    }
    
}
