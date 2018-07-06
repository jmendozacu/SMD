<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_DeleteRequest_Notifier
{
    /**
     * @param $customerId
     * @param $comment
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function notify($customerId, $comment)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');

        $customer->load($customerId);

        /** @var Mage_Core_Model_Email_Template $email */
        $email = Mage::getModel('core/email_template');
        $email->sendTransactional(
            Mage::getStoreConfig('amgdpr/deletion_notification/template'),
            Mage::getStoreConfig('amgdpr/deletion_notification/from'),
            $customer->getEmail(),
            $customer->getName(),
            array(
                'comment' => $comment,
                'customer' => $customer
            ),
            $customer->getStore()->getId()
        );
    }
}
