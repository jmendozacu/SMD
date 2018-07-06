<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_ConsentLog extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amgdpr/consentLog');
    }

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Varien_Exception
     */
    protected function _beforeSave()
    {
        if (!$this->getData('date_consented')) {
            $this->setData(
                'date_consented',
                Mage::getSingleton('core/date')->gmtDate()
            );
        }

        return parent::_beforeSave();
    }

    public function acceptLastVersion($customerId)
    {
        /** @var Amasty_Gdpr_Model_ConsentLog $consentLog */
        $consentLog = Mage::getModel('amgdpr/consentLog');

        $modelContent = Mage::getModel('amgdpr/privacyPolicy')->getCurrentPolicy();

        $consentLog
            ->setData(array(
                'customer_id' => $customerId,
                'policy_version' => $modelContent->getData('policy_version')
            ))
            ->save();

        Mage::getSingleton('amgdpr/actionLog')->logAction('consent_given', $customerId);
    }
}
