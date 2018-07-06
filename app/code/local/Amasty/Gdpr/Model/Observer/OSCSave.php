<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_OSCSave
{
    public function execute(Varien_Object $observer)
    {
        /** @var Mage_Core_Controller_Varien_Action $action */
        $action = $observer->getData('controller_action');

        if (!$action->getRequest()->getParam('amgdpr_agree')) {
            return;
        }

        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');

        if (!$customerSession->getId()) {
            return;
        }

        /** @var Amasty_Gdpr_Model_ConsentLog $consentLog */
        $consentLog = Mage::getModel('amgdpr/consentLog');

        $consentLog->acceptLastVersion($customerSession->getId());
    }
}
