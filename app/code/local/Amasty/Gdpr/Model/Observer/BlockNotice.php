<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_BlockNotice
{
    public function execute(Varien_Event_Observer $observer)
    {
        if (version_compare(Mage::getVersion(), '1.8.1.0', '>=')) {
            $cmsBlock = $observer->getObject();
            if (is_a($cmsBlock, 'Mage_Cms_Model_Block')
                && Mage::getStoreConfig('amgdpr/cookie_policy/enabled')
            ) {
                $blockIdentifier = Mage::helper('core/cookie')->getCookieRestrictionNoticeCmsBlockIdentifier();
                $notificationText = Mage::getStoreConfig('amgdpr/cookie_policy/notification_text');
                if ($notificationText
                    && $blockIdentifier == $cmsBlock->getIdentifier()
                ) {
                    $cmsBlock->setContent($notificationText);
                }
            }
        }
    }
}
