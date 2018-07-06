<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_LoginController extends Mage_Core_Controller_Front_Action
{
    public function loginAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            $customerId = (int)$params['customer_id'];
            $requestKey = $params['key'];
            $generatedKey = Mage::getSingleton('amgdpr/consentQueue')->generateKey($customerId);
            if ($requestKey == $generatedKey) {
                /** @var Mage_Customer_Model_Session $customerSession */
                $customerSession = Mage::getSingleton('customer/session');

                $customerIsLoggedIn = $customerSession->isLoggedIn();
                if ($customerIsLoggedIn && ($customerId != $customerSession->getCustomerId())) {
                    $customerSession->logout();
                    $customerIsLoggedIn = false;
                }

                if (!$customerIsLoggedIn) {
                    /** @var Mage_Customer_Model_Customer $customer */
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    if ($customer->getId()) {
                        $customerSession->setCustomerAsLoggedIn($customer);
                    }
                }

                /** @var Amasty_Gdpr_Model_ConsentLog $consentLog */
                $consentLog = Mage::getModel('amgdpr/consentLog');
                $consentLog->acceptLastVersion($customerSession->getId());
                $customerSession->addSuccess($this->__('Thank you for your cooperation. Your consent was recorded.'));
            }
        } catch (Exception $exception) {
            Mage::getSingleton('core/session')->addError($this->__('Something went wrong.'));
            Mage::logException($exception);
        }

        $this->_redirect('customer/account');
    }
}