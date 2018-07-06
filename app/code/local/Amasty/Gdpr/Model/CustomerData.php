<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_CustomerData extends Mage_Core_Model_Abstract
{
    protected $allowedAttributes = array(
        'customer' => array(
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',

            'email',
            'dob',
            'gender'
        ),
        'customer_address' => array(
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',

            'company',
            'street',
            'city',
            'country_id',
            'region',
            'postcode',
            'telephone',
            'fax'
        )
    );

    public function getAttributeCodes($type)
    {
        $attributeCodes = array();

        if (isset($this->allowedAttributes[$type])) {
            $attributeCodes = $this->allowedAttributes[$type];
        }

        return $attributeCodes;
    }

    /**
     * @param $customerId
     * @return array
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function getPersonalData($customerId)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $data = array_merge(
            $this->getCustomerEavData($customer),
            $this->getAddressEavData($customer)
        );

        return $data;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     * @throws Varien_Exception
     */
    protected function getCustomerEavData(Mage_Customer_Model_Customer $customer)
    {
        return $this->collectAttributeValues($customer, $this->getAttributes('customer'));
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    protected function getAddressEavData(Mage_Customer_Model_Customer $customer)
    {
        $attributes = $this->getAttributes('customer_address');

        /** @var Mage_Customer_Model_Entity_Address_Collection $addressCollection */
        $addressCollection = Mage::getResourceModel('customer/address_collection');

        $addressCollection
            ->setCustomerFilter($customer)
            ->addAttributeToSelect('*');

        $result = array();

        $i = 0;
        foreach ($addressCollection as $address) {
            $i++;
            $result = array_merge(
                $result,
                $this->collectAttributeValues($address, $attributes, "Address #$i ")
            );
        }

        return $result;
    }

    /**
     * @param Varien_Object $entity
     * @param $attributes
     * @param string $namePrefix
     * @return array
     * @throws Varien_Exception
     */
    protected function collectAttributeValues(Varien_Object $entity, $attributes, $namePrefix = '')
    {
        $result = array();

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $value = $entity->getData($attribute->getName());

            if (!empty($value)) {
                $result [] = array(
                    $namePrefix . $attribute->getFrontendLabel(),
                    $value
                );
            }
        }

        return $result;
    }

    /**
     * @param $entityCode
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     * @throws Varien_Exception
     */
    protected function getAttributes($entityCode)
    {
        /** @var Mage_Eav_Model_Entity_Type $customerEntityType */
        $customerEntityType = Mage::getModel('eav/entity_type')->loadByCode($entityCode);

        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributeCollection */
        $attributeCollection = Mage::getResourceModel('eav/entity_attribute_collection');

        $attributeCollection->setEntityTypeFilter($customerEntityType);

        $attributeCollection->addFieldToFilter(
            'attribute_code',
            array('in' => $this->allowedAttributes[$entityCode])
        );

        return $attributeCollection;
    }
}
