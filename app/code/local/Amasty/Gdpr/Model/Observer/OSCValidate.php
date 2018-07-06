<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_OSCValidate
{
    /**
     * @param Varien_Object $observer
     * @throws Varien_Exception
     */
    public function execute(Varien_Object $observer)
    {
        /** @var Mage_Checkout_OnepageController $action */
        $action = $observer->getData('controller_action');

        if ($action->getRequest()->getParam('amgdpr_agree')) {
            return;
        }

        /** @var Amasty_Gdpr_Block_Checkbox_Checkout $checkboxBlock */
        $checkboxBlock = $action->getLayout()->getBlockSingleton('amgdpr/checkbox_checkout');
        if (!$checkboxBlock->isVisible()) {
            return;
        }

        $result = array(
            'success' => false,
            'error' => true,
            'error_messages' => $action->__('Please agree to all the terms and conditions before placing the order.')
        );

        $action->getResponse()->setHeader('Content-type', 'application/json', true);
        $action->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        $action->setFlag('', 'no-dispatch', true);
    }
}
