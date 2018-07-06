<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_CheckDisabledCustomers
{
    public function execute($observer)
    {
        /** @var \Mage_Customer_Model_Session $customerSession */
        $customerSession = $observer->getCustomerSession();

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $customerSession->getCustomer();

        $email = $customer->getData('email');
        $emailWithoutDomain = substr($email, 0, strrpos($email, '@'));
        if ($emailWithoutDomain == Amasty_Gdpr_Model_AnonymizeModel::DELETED_EMAIL) {
            $customerSession->getCookie()->delete($customerSession->getSessionName());
            Mage::app()->getResponse()->setRedirect('/');
        }
    }
}
