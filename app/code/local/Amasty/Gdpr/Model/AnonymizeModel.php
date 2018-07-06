<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_AnonymizeModel extends Mage_Core_Model_Abstract
{
    const DELETED_EMAIL = '-';
    const RANDOM_LENGTH = 5;
    const EMAIL_TEMPLATE_CODE = 'amgdpr_anonimization';

    protected $isDeleting = false;

    /**
     * Remove customer details from the address
     *
     * @param Mage_Sales_Model_Order_Address|Mage_Sales_Model_Quote_Address|Mage_Customer_Model_Address $address
     */
    protected function anonymizeAddress($address)
    {
        $attributeCodes = Mage::getSingleton('amgdpr/customerData')->getAttributeCodes('customer_address');

        foreach ($attributeCodes as $code) {
            switch ($code) {
                case 'telephone':
                case 'fax':
                    $randomString = '0000000';
                    break;
                case 'country_id':
                case 'region':
                    continue 2;
                default:
                    $randomString = $this->getRandom();
            }
            $address->setData($code, $randomString);
        }
    }

    /**
     * Remove customer details from a quote or order
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $obj
     */
    protected function anonymizeSaleData($obj)
    {
        $obj->setCustomerFirstname($this->getRandom());
        $obj->setCustomerMiddlename($this->getRandom());
        $obj->setCustomerLastname($this->getRandom());
        $obj->setCustomerEmail($this->getRandomEmail());
        if ($obj->getBillingAddress()) {
            $this->anonymizeAddress($obj->getBillingAddress());
        }
        if ($obj->getShippingAddress()) {
            $this->anonymizeAddress($obj->getShippingAddress());
        }
        $obj->save();
    }

    /**
     * @param $customerId
     * @throws Mage_Core_Exception
     */
    public function deleteCustomer($customerId)
    {
        $this->isDeleting = true;
        $this->anonymizeCustomer($customerId);
        Mage::getSingleton('amgdpr/actionLog')->logAction('delete_request_approved', $customerId);
    }

    /**
     * @param $customerId
     * @throws Mage_Core_Exception
     */
    public function anonymizeCustomer($customerId)
    {
        Mage::dispatchEvent(
            'before_amgdpr_customer_anonymisation',
            array('customerId' => $customerId, 'isDeleting' => $this->isDeleting)
        );

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getSingleton('customer/customer')->load($customerId);

        $resourcesToAnonymise = array(
            'sales/order_collection',
            'sales/quote_collection'
        );

        foreach ($resourcesToAnonymise as $resource) {
            $entities = Mage::getResourceModel($resource)
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId());
            foreach ($entities as $entity) {
                $this->anonymizeSaleData($entity);
            }
        }

        // Delete customer newsletter subscription
        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
        if ($subscriber->getId()) {
            $subscriber->unsubscribe();
            $subscriber->delete();
        }

        $this->anonymizeAccountInformation($customer);
        if (!$this->isDeleting) {
            Mage::getSingleton('amgdpr/actionLog')->logAction('data_anonymised_by_customer', $customerId);
        }

        Mage::dispatchEvent(
            'after_amgdpr_customer_anonymisation',
            array('customerId' => $customerId, 'isDeleting' => $this->isDeleting)
        );
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     */
    public function anonymizeAccountInformation($customer)
    {
        $attributeCodes = Mage::getSingleton('amgdpr/customerData')->getAttributeCodes('customer');
        $oldEmail = $customer->getEmail();

        foreach ($attributeCodes as $attributeCode) {
            switch ($attributeCode) {
                case 'email':
                    $randomString = $this->getRandomEmail();
                    break;
                case 'dob':
                case 'gender':
                    $randomString = '';
                    break;
                default:
                    $randomString = $this->getRandom();
            }
            $customer->setData($attributeCode, $randomString);
        }

        if (!$this->isDeleting) {
            $this->sendConfirmationEmail($oldEmail, $customer);
            Mage::getResourceModel('amgdpr/deleteRequest_collection')->deleteByCustomerId($customer->getId());
        }
        $customer->save();

        $addresses = $customer->getAddresses();
        /** @var \Mage_Customer_Model_Address $address */
        foreach ($addresses as $address) {
            $this->anonymizeAddress($address);
            $address->save();
        }

    }

    /**
     * @param string $realEmail
     * @param Mage_Customer_Model_Customer $customer
     * @throws Mage_Core_Exception
     */
    public function sendConfirmationEmail($realEmail, $customer)
    {
        /** @var Mage_Core_Model_Email_Template $email */
        $email = Mage::getModel('core/email_template');
        $email->sendTransactional(
            Mage::getStoreConfig('amgdpr/anonymisation_notification/template'),
            Mage::getStoreConfig('amgdpr/anonymisation_notification/from'),
            $realEmail,
            $customer->getName(),
            array(
                'anonymousEmail' => $customer->getEmail()
            ),
            0);
    }

    public function getRandomEmail()
    {
        $email = self::DELETED_EMAIL;
        if (!$this->isDeleting) {
            $email = $this->getRandom();
        }
        $email = $email . '@' . $this->getRandomString() . '.com';

        if ($this->isEmailExists($email)) {
            $email = $this->getRandomEmail();
        }

        return $email;
    }

    public function getRandom()
    {
        $rand = '-';
        if (!$this->isDeleting) {
            $rand = 'anonymous' . $this->getRandomString();
        }

        return $rand;
    }

    public function isEmailExists($email)
    {
        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = Mage::getResourceModel('customer/customer_collection');

        return (bool)$collection->addFieldToFilter('email', $email)->getSize();
    }

    protected function getRandomString()
    {
        return bin2hex(openssl_random_pseudo_bytes(self::RANDOM_LENGTH));
    }
}
