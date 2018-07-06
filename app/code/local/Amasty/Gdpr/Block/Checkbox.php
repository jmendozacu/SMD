<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


abstract class Amasty_Gdpr_Block_Checkbox extends Mage_Core_Block_Template
{
    abstract protected function isEnabledInSettings();

    /**
     * @return bool
     * @throws Varien_Exception
     */
    public function isVisible()
    {
        if (!$this->isEnabledInSettings()) {
            return false;
        }

        if (!Mage::getModel('amgdpr/privacyPolicy')->getCurrentPolicy()->getId()) {
            return false;
        }

        if (Mage::getStoreConfigFlag('amgdpr/privacy_checkbox/eea_only')) {
            /** @var Amasty_Gdpr_Model_Visitor $visitor */
            $visitor = Mage::getSingleton('amgdpr/visitor');

            if (!$visitor->isEEACustomer()) {
                return false;
            }
        }

        return true;
    }

    public function getConsentText()
    {
        return Mage::getStoreConfig('amgdpr/privacy_checkbox/consent_text');
    }

    /**
     * @return string
     * @throws Varien_Exception
     */
    protected function _toHtml()
    {
        if ($this->isVisible()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
