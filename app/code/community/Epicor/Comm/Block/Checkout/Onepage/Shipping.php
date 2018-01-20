<?php

class Epicor_Comm_Block_Checkout_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{

    public function getAddressesHtmlSelect($type)
    {

        $html = '';

        if ($this->isCustomerLoggedIn()) {
            $options = array();

            $addresses = array();
            $loadAddresses = true;
            $aType = ($type == 'billing') ? 'invoice' : 'delivery';
            $transportObject = new Varien_Object;
            $transportObject->setAddresses($addresses);
            $transportObject->setLoadAddresses($loadAddresses);
            Mage::dispatchEvent('epicor_comm_onepage_get_checkout_addresses', array('quote' => Mage::getSingleton('checkout/session')->getQuote(), 'type' => $aType, 'restrict_by_type' => $this->restrictAddressTypes(), 'addresses' => $transportObject));
            $addresses = $transportObject->getAddresses();
            $loadAddresses = $transportObject->getLoadAddresses();

            if ($loadAddresses) {
                $addresses = ($this->restrictAddressTypes()) ? $this->getCustomer()->getAddressesByType($aType) : $this->getCustomer()->getAddresses();
            }

            $fastFormat = Mage::getStoreConfigFlag('customer/address_templates/checkout_disable');

            foreach ($addresses as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $fastFormat ? $this->fastFormat($address) : $address->format('oneline'),
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();

            if (empty($addressId)) {
                if ($this->isMasquerading()) {
                    $addCode = $this->getAddress()->getErpAddressCode();
                     $helper = Mage::helper('epicor_comm');
                    /* @var $helper Epicor_Comm_Helper_Data */
                    $erpAccount = $helper->getErpAccountInfo();
                    if ($erpAccount && $addCode) {
                        $addressColl = Mage::getModel('epicor_comm/customer_erpaccount_address')->getCollection();
                        /* @var $addressColl Epicor_Comm_Model_Resource_Customer_Erpaccount_Address_Collection */
                        $addressColl->addFieldToFilter('erp_code', $addCode);
                        $addressColl->addFieldToFilter('erp_customer_group_code', $erpAccount->getErpCode());
                        $address = $addressColl->getFirstItem();
                        $addressId = 'erpaddress_' . $address->getId();
                    }
                } else {
                    if ($type == 'billing') {
                        $address = $this->getCustomer()->getPrimaryBillingAddress();
                    } else {
                        $address = $this->getCustomer()->getPrimaryShippingAddress();
                    }

                    if ($address) {
                        $addressId = $address->getId();
                    }
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                    ->setName($type . '_address_id')
                    ->setId($type . '-address-select')
                    ->setClass('address-select')
                    ->setExtraParams('onchange="' . $type . '.newAddress(!this.value)"')
                    ->setValue($addressId)
                    ->setOptions($options);

            if ($this->canAddNew()) {
                $select->addOption('', Mage::helper('checkout')->__('New Address'));
            }

            $html = $select->getHtml();
        }

        return $html;
    }

    public function restrictAddressTypes()
    {
        return Mage::getStoreConfigFlag('Epicor_Comm/address/force_type');
    }

    public function canAddNew()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->customerAddressPermissionCheck('create');
    }

    public function isMasquerading()
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->isMasquerading();
    }
    
    public function hideNameFields()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */

        $contact = $quote->getEccSalesrepChosenCustomerId() . $quote->getEccSalesrepChosenCustomerInfo();
        return false; //$this->getCustomer()->isSalesRep() && $this->isMasquerading() && !empty($contact);
    }
    
    public function getAddress()
    {
        $address = parent::getAddress();
        Mage::dispatchEvent('epicor_comm_onepage_shipping_get_address', array('quote' => Mage::getSingleton('checkout/session')->getQuote(), 'address' => $address));
        return $address;
    }
    public function displayEmail()
    {
        return Mage::getStoreConfigFlag('customer/address/display_email');
    }
    public function displayMobilePhone()
    {
        return Mage::getStoreConfigFlag('customer/address/display_mobile_phone');
    }
    public function displayInstructions()
    {
        return Mage::getStoreConfigFlag('customer/address/display_instructions');
    }

    /**
     * Formats an address based on a static format ratehr than configurable
     *
     * @param Mage_Customer_Model_Address $address
     */
    protected function fastFormat($address)
    {
        //
        //{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}
        //{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var city}}, {{var region}}
        //{{var postcode}}, {{var country}}
        //

        $addressTxt = '';
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        if ($helper->isMasquerading() == false) {
            $addressTxt .= $address->getCustomer()->getName() . ', ';
        }

        $addressTxt .= ($address->getCompany()) ? $address->getCompany() : '';
        $addressTxt .= ', ' . $address->getStreet1();
        $addressTxt .= ($address->getStreet2()) ? ', ' . $address->getStreet2() : '';
        $addressTxt .= ($address->getStreet3()) ? ', ' . $address->getStreet3() : '';
        $addressTxt .= ($address->getStreet4()) ? ', ' . $address->getStreet4() : '';
        $addressTxt .= ($address->getCity()) ? ', ' . $address->getCity() : '';
        $addressTxt .= ($address->getRegion()) ? ', ' . $address->getRegion() : '';
        $addressTxt .= ($address->getPostcode()) ? ' ' . $address->getPostcode() : '';
        $addressTxt .= ($address->getCountry()) ? ', ' . $address->getCountryModel()->getName() : '';

        return $addressTxt;
    }

}
