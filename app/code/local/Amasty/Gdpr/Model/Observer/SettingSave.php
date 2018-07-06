<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Observer_SettingSave
{
    public function execute(Varien_Event_Observer $observer)
    {
        if ('amgdpr' == $observer->getObject()->getSection()) {
            $settings = $observer->getObject()->getData();
            if (isset($settings['groups']['cookie_policy']['fields']['enabled']['value'])) {
                Mage::getConfig()->saveConfig('web/cookie/cookie_restriction', $settings['groups']['cookie_policy']['fields']['enabled']['value']);
            }
        }

        if ('web' == $observer->getObject()->getSection()) {
            $settings = $observer->getObject()->getData();
            if (isset($settings['groups']['cookie']['fields']['cookie_restriction']['value'])) {
                Mage::getConfig()->saveConfig('amgdpr/cookie_policy/enabled', $settings['groups']['cookie']['fields']['cookie_restriction']['value']);
            }
        }
    }
}
