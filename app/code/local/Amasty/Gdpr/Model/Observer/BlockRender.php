<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_BlockRender
{
    public function execute(Varien_Object $observer)
    {
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getData('block');
        $transport = $observer->getData('transport');

        if (version_compare(Mage::getVersion(), '1.8.1.0', '<')
            && is_a($block, 'Mage_Page_Block_Html_Notices')
            && Mage::getStoreConfig('amgdpr/cookie_policy/enabled')
            && $notificationText = Mage::getStoreConfig('amgdpr/cookie_policy/notification_text')
        ) {
            $html = $transport->getHtml();
            preg_match('@<div.*?notice-cookie.*?(<p>.*?</p>)@s', $html, $matches);
            if (isset($matches[1])) {
                $transport->setHtml(
                    str_replace($matches[1], $notificationText, $html)
                );
            }
        }

        if (is_a($block, 'Mage_Checkout_Block_Agreements')) {
            $checkboxBlock = $block->getLayout()->createBlock('amgdpr/checkbox_checkout');
            $transport->setHtml(
                $transport->getHtml() . $checkboxBlock->toHtml()
            );
        }
    }
}
