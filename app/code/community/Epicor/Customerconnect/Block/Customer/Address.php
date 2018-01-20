<?php

/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method void setOnRight(bool $bool)
 * @method bool getOnRight()
 */
class Epicor_Customerconnect_Block_Customer_Address extends Mage_Directory_Block_Data
{

    /**
     *  @var Varien_Object 
     */
    protected $_addressData;
    protected $_countryCode;
    protected $_showAddressCode = true;

    public function _construct()
    {
        parent::_construct();
        $this->_addressData = array();
        $this->setTemplate('customerconnect/address.phtml');
        $this->setOnRight(false);
        $this->setShowName(true);
    }

    public function getJsonEncodedData()
    {
        $detailsArray = array(
            'name' => $this->_addressData->getName(),
            'address1' => $this->_addressData->getAddress1(),
            'address2' => $this->_addressData->getAddress2(),
            'address3' => $this->_addressData->getAddress3(),
            'city' => $this->_addressData->getCity(),
            'county' => $this->_addressData->getCounty(),
            'country' => $this->_addressData->getCountry(),
            'postcode' => $this->_addressData->getPostcode(),
            'email' => $this->_addressData->getEmailAddress(),
            'telephone' => $this->_addressData->getTelephoneNumber(),
            'fax' => $this->_addressData->getFaxNumber(),
            'address_code' => $this->_addressData->getAddressCode()
        );
        return json_encode($detailsArray);
    }

    public function getAddressCode()
    {
        return $this->_addressData->getAddressCode();
    }

    public function getName()
    {
        return $this->_addressData->getName();
    }
    
    public function getCompany()
    {
        return $this->_addressData->getCompany();
    }

    public function getAddress1()
    {
        return $this->_addressData->getAddress1();
    }

    public function getAddress2()
    {
        return $this->_addressData->getAddress2();
    }

    public function getAddress3()
    {
        return $this->_addressData->getAddress3();
    }

    public function getStreet()
    {
        $street = $this->_addressData->getAddress1();
        $street .= $this->_addressData->getAddress2() ? ', ' . $this->_addressData->getAddress2() : '';
        $street .= $this->_addressData->getAddress3() ? ', ' . $this->_addressData->getAddress3() : '';
        return $street;
    }

    public function getCity()
    {
        return $this->_addressData->getCity();
    }

    public function getCounty()
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $region = $helper->getRegionFromCountyName($this->getCountryCode(), $this->_addressData->getCounty());

        return ($region) ? $region->getName() : $this->_addressData->getCounty();
    }

    public function getRegionId()
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $region = $helper->getRegionFromCountyName($this->getCountryCode(), $this->_addressData->getCounty());

        $regionId = ($region) ? $region->getId() : 0;
        return $regionId;
    }

    public function getPostcode()
    {
        return $this->_addressData->getPostcode();
    }

    public function getCountryCode()
    {

        if (is_null($this->_countryCode)) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $this->_countryCode = $helper->getCountryCodeForDisplay($this->_addressData->getCountry(), $helper::ERP_TO_MAGENTO);
        }

        return $this->_countryCode;
    }

    public function getCountry()
    {
        try {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */

            return $helper->getCountryName($this->getCountryCode());
        } catch (Exception $e) {
            return $this->_addressData->getCountry();
        }
    }

    public function getTelephoneNumber()
    {
        return $this->_addressData->getTelephoneNumber();
    }

    public function getFaxNumber()
    {
        return $this->_addressData->getFaxNumber();
    }

    public function getEmail()
    {
        return $this->_addressData->getEmailAddress();
    }

    public function getCarriageText()
    {
        return $this->_addressData->getCarriageText() ? : $this->_addressData->getInstructions();
    }

    public function getAddressesHtmlSelect()
    {

        $options = array();

        $helper = Mage::helper('epicor_comm/Messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        $restrict = Mage::getStoreConfigFlag('Epicor_Comm/address/force_type');


        $type = $this->getAddressType();

        if ($type == 'quote') {
            $type = 'invoice';
        }

        $addressId = null;
        $addresses = ($restrict) ? $customer->getAddressesByType($type) : $customer->getAddresses();
        foreach ($addresses as $address) {
            /* @var $address Mage_Customer_Model_Address */
            $formatted = trim(ltrim(trim(str_replace($customer->getName() . ',', '', $address->format('oneline'))),','));
            $options[] = array(
                'value' => $address->getId(),
                'label' => $formatted,
                'params' => array(
                    'data-iscustom' => $address->getIsCustom(),
                    'data-address' => htmlentities(json_encode(array(
                        'addressCode' => $address->getErpAddressCode(),
                        'name' => $helper->stripNonPrintableChars($address->getCompany()),
                        'address1' => $helper->stripNonPrintableChars($address->getStreet1()),
                        'address2' => $helper->stripNonPrintableChars($address->getStreet2()),
                        'address3' => $helper->stripNonPrintableChars($address->getStreet3()),
                        'city' => $helper->stripNonPrintableChars($address->getCity()),
                        'county' => $helper->stripNonPrintableChars($helper->getRegionNameOrCode($address->getCountry_id(), ($address->getRegionId() ? $address->getRegionId() : $address->getRegion()))),
//                        'county' => $helper->stripNonPrintableChars($address->getRegion()),
                        'country' => $helper->getErpCountryCode($address->getCountry_id()),
                        'postcode' => $helper->stripNonPrintableChars($address->getPostcode()),
                        'telephoneNumber' => $helper->stripNonPrintableChars($address->getTelephone()),
                        'mobileNumber' => $helper->stripNonPrintableChars($address->getMobileNumber()),
                        'faxNumber' => $helper->stripNonPrintableChars($address->getFax()),
                    )))
                )
            );
            if ($this->_addressData->getAddressCode() == $address->getErpAddressCode()) {
                $addressId = $address->getId();
            }
        }

        if ($this->_addressData->getAddressCode() == '') {
            $options[] = array(
                'value' => '',
                'label' => '',
                'params' => array(
                    'address_data' => $this->_addressData->getData(),
                    'id' => 'custom_address_selected',
                    'selected' => 'selected',
                    'full_country' => $this->getCountry(),
                    'data-address' => htmlentities(json_encode($this->_addressData->getData()))
                )
            );
        }
        $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($this->getAddressType() . '_address_id')
                ->setId($this->getAddressType() . '-address-select')
                ->setClass('address-select')
                ->setValue($addressId)
                ->setOptions($options);

        if ($this->canAddNew()) {
            $select->addOption('', Mage::helper('checkout')->__('New Address'));
        }
        $html = $select->getHtml();

        return $html;
    }

    public function setAddressFromCustomerAddress($data)
    {
        /* @var $data Mage_Customer_Model_Address */
        $this->_addressData = new Varien_Object(
                array(
            'name' => $data->getName(),
            'company' => $data->getCompany(),
            'address1' => $data->getStreet1(),
            'address2' => $data->getStreet2(),
            'address3' => $data->getStreet3(),
            'city' => $data->getCity(),
            'county' => $data->getCounty() ?: $data->getRegionCode(),
            'country' => $data->getCountry(),
            'postcode' => $data->getPostcode(),
            'email' => $data->getEmail(),
            'telephone_number' => $data->getTelephone(),
            'fax' => $data->getFax(),
            'address_code' => $data->getErpAddressCode(),
            'instructions' => $data->getInstructions()
                )
        );

        return $this;
    }

    private function canAddNew()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Comm_Helper_Data */

        return $helper->customerAddressPermissionCheck('create');
    }

    public function isErpAddress()
    {
        return $this->_addressData->getAddressCode() != '';
    }

    public function getAddressesFormHtml()
    {
        $type = 'delivery';

        $form = $this->getLayout()->createBlock('customerconnect/customer_editableaddress')
                        ->setAddressType($type)
                        ->setFieldnamePrefix($type . '_address[')
                        ->setFieldnameSuffix(']')
                        ->setShowAddressCode(false)
                        ->setAddressData(new Varien_Object($this->_addressData->getData()))->toHtml();

        return $form;
    }

    public function setAddressData($data)
    {
        $this->_addressData = $data;
        return $this;
    }
    
    public function setShowAddressCode($show)
    {
        $this->_showAddressCode = $show;
        return $this;
    }
    
    public function getShowAddressCode()
    {
        return $this->_showAddressCode;
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

}
