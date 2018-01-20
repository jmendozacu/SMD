<?php

/**
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Checkout_Multishipping_Addresses extends Mage_Checkout_Block_Multishipping_Addresses
{

    public function restrictAddressTypes()
    {
        return Mage::getStoreConfigFlag('Epicor_Comm/address/force_type');
    }

    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();

            $addresses = ($this->restrictAddressTypes()) ? $this->getCustomer()->getAddressesByType('delivery') : $this->getCustomer()->getAddresses();

            foreach ($addresses as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $this->setData('address_options', $options);
        }

        return $options;
    }
    
    public function canAddNew()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->customerAddressPermissionCheck('create');
    }

}
