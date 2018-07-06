<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_PrivacyPolicy extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected function _construct()
    {
        $this->_init('amgdpr/privacyPolicy');
    }

    public function getStatuses()
    {
        $statuses = array(
            self::STATUS_DISABLED => 'Disabled',
            self::STATUS_ENABLED => 'Enabled'
        );

        return $statuses;
    }

    public function getCurrentPolicy()
    {
        /** @var Amasty_Gdpr_Model_Resource_PrivacyPolicy_Collection $collection */
        $collection = $this->getCollection();
        $collection
            ->joinContent(Mage::app()->getStore()->getId())
            ->addFieldToFilter('status', 1);

        return $collection->getFirstItem();
    }

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Varien_Exception
     */
    protected function _beforeSave()
    {
        $now = Mage::getSingleton('core/date')->gmtDate();

        if (!$this->getData('created_at')) {
            $this->setData('created_at', $now);
        }

        $this->setData('date_last_edited',$now);

        return parent::_beforeSave();
    }
}
