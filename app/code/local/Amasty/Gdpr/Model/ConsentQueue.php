<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


/**
 * @method int getId()
 * @method Amasty_Gdpr_Model_ConsentQueue setId(int $value)
 * @method int getCustomerId()
 * @method Amasty_Gdpr_Model_ConsentQueue setCustomerId(int $value)
 * @method int getStatus()
 * @method Amasty_Gdpr_Model_ConsentQueue setStatus(int $value)
 *
 * Class Amasty_Gdpr_Model_ConsentQueue
 */
class Amasty_Gdpr_Model_ConsentQueue extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;

    protected function _construct()
    {
        $this->_init('amgdpr/consentQueue');
    }

    public function sendEmails()
    {
        $lockFname = Mage::getBaseDir('tmp'). DS . 'amasty_gdpr_send_emails.lock';
        $fp = fopen($lockFname, 'w');

        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            return;
        }

        $this->getCollection()->sendEmails();

        fclose($fp);
    }

    public function sendEmail()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        /** @var Mage_Core_Model_Email_Template $email */
        $email = Mage::getModel('core/email_template');

        try {
            $status = self::STATUS_FAIL;
            $result = $email->sendTransactional(
                Mage::getStoreConfig('amgdpr/consent_notification/template'),
                Mage::getStoreConfig('amgdpr/consent_notification/from'),
                $customer->getData('email'),
                $customer->getName(),
                array(
                    'customer' => $customer,
                    'accountUrl' => $this->getAccountUrl($this->getCustomerId())
                ),
                $customer->getStore()->getId()
            );
            if ($result->getData('sent_success') === true) {
                $status = self::STATUS_SUCCESS;
            }
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        try {
            $this->setStatus($status);
            $this->save();
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    public function getAvailableStatuses()
    {
        /** @var Amasty_Gdpr_Helper_Data $helper */
        $helper = Mage::helper('amgdpr');

        return array(
            self::STATUS_PENDING => $helper->__('Pending'),
            self::STATUS_SUCCESS => $helper->__('Success'),
            self::STATUS_FAIL => $helper->__('Fail'),
        );
    }

    public function addQueueLinkNotice()
    {
        $queueGridLink = Mage::helper("adminhtml")->getUrl('adminhtml/amgdpr_consentQueue');
        Mage::getSingleton('adminhtml/session')->addNotice(
            Mage::helper('amgdpr')->__(
                'You can see queue of sending e-mail consents <a href="%s" target="_blank">here</a>',
                $queueGridLink
            )
        );
    }

    public function getAccountUrl($customerId)
    {
        return Mage::getUrl(
            'amgdpr/login/login',
            array(
                'customer_id' => $customerId,
                'key' => $this->generateKey($customerId)
            )
        );
    }

    public function generateKey($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        return sha1($customerId . $customer->getPasswordHash());
    }
}
