<?php

class Epicor_Comm_Block_Checkout_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{

    public function _construct()
    {
        $this->setAddressType('noaddresses');   // allows guests and new accounts with no addresses to checkout
        parent::_construct();
    }

    public function getAddressesHtmlSelect($type)
    {
        $html = '';
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            $addresses = array();
            $loadAddresses = true;
            $aType = ($type == 'billing') ? 'invoice' : 'delivery';

            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */

            Mage::register('billing_address_checkout', true);

            $transportObject = new Varien_Object;
            $transportObject->setAddresses($addresses);
            $transportObject->setLoadAddresses($loadAddresses);
            Mage::dispatchEvent('epicor_comm_onepage_get_checkout_addresses', array('quote' => Mage::getSingleton('checkout/session')->getQuote(), 'type' => $aType, 'restrict_by_type' => $this->restrictAddressTypes(), 'addresses' => $transportObject));
            $addresses = $transportObject->getAddresses();
            $loadAddresses = $transportObject->getLoadAddresses();

            if ($loadAddresses) {
                $addresses = ($this->restrictAddressTypes()) ? $this->getCustomer()->getAddressesByType($aType,true) : $this->getCustomer()->getAddresses();
            }

            $this->setForcedAddressTypes($this->restrictAddressTypes());

            $fastFormat = Mage::getStoreConfigFlag('customer/address_templates/checkout_disable');

            foreach ($addresses as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $fastFormat ? $this->fastFormat($address) : $address->format('oneline'),
                    'params' => array(
                        'data-iscustom' => $this->restrictAddressTypes() ? $address->getIsCustom() : 1
                    )
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();

            if (empty($addressId)) {
                if ($type == 'billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
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

            Mage::unregister('billing_address_checkout');

            $html = $select->getHtml();
        }

        return $html;
    }

    public function restrictAddressTypes()
    {
        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */
        $force = Mage::getStoreConfigFlag('Epicor_Comm/address/force_type');
        $session = Mage::getSingleton('customer/session');
        $customer = $session->getCustomer();       
        $getAccountType = $customer->getEccErpAccountType();
        if($getAccountType =="guest") {
           $force = false; 
        } else {        
            if ($force == false && $helper->contractsEnabled()) {
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                /* @var $quote Epicor_Comm_Model_Quote */

                $contracts = $helper->getQuoteContracts($quote);
                if (empty($contracts) == false) {
                    $force = true;
                }
            }
        }
        return $force;
    }

    public function canAddNew()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->createBillingAddress();
    }

    public function isMasquerading()
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->isMasquerading();
    }

    public function hideNameFields()
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        return $this->getCustomer()->isCustomer() || $helper->isMasquerading();
    }
    
    public function isPOMandatory(){
        
        $mandatory = false;
        $flag = Mage::getStoreConfigFlag('checkout/options/po_mandatory');

        $helper = Mage::helper('epicor_comm');
        /*@var $helper Epicor_Comm_Helper_Data */
        $erpAccount = $helper->getErpAccountInfo();
        $po = $erpAccount->getPoMandatory();
        
        if($po == 1){
            $mandatory = true;
        }else if($po == null && $flag){
            $mandatory = true;
        }
        
        return $mandatory;
        
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

        if ($this->getCustomer()->isGuest()) {
            $addressTxt .= $address->getCustomer()->getName() . ', ';
        }

        $addressTxt .= ($address->getCompany()) ? $address->getCompany() . ', ' : '';
        $addressTxt .= ' ' . $address->getStreet1();
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
