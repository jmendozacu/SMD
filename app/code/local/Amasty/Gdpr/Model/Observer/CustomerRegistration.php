<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_CustomerRegistration
{
    public function execute(Varien_Object $observer)
    {
        /** @var Mage_Customer_AccountController $controller */
        $controller = $observer->getData('account_controller');

        if (!$controller->getRequest()->getParam('amgdpr_agree')) {
            return;
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getData('customer');

        /** @var Amasty_Gdpr_Model_ConsentLog $consentLog */
        $consentLog = Mage::getModel('amgdpr/consentLog');

        $consentLog->acceptLastVersion($customer->getId());
    }
}
