<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Visitor
{
    /**
     * @return bool
     * @throws Varien_Exception
     */
    public function isEEACustomer()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        /** @var Mage_Checkout_Model_Session $checkoutSession */
        $checkoutSession = Mage::getSingleton('checkout/session');

        if ($countryCode = $checkoutSession->getQuote()->getBillingAddress()->getCountry()) {
            return Mage::getSingleton('amgdpr/country')->isEEACountry($countryCode);
        }

        if ($customer && ($address = $customer->getPrimaryBillingAddress())) {
            if ($countryCode = $address->getCountry()) {
                return Mage::getSingleton('amgdpr/country')->isEEACountry($countryCode);
            }
        }

        if ($countryCode = $this->locate()) {
            return Mage::getSingleton('amgdpr/country')->isEEACountry($countryCode);
        } else {
            return false;
        }
    }

    protected function locate()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        if ($session->hasData('amgdpr_country')) {
            return $session->getData('amgdpr_country');
        }

        /** @var Amasty_Geoip_Model_Geolocation $geolocation */
        $geolocation = Mage::getSingleton('amgeoip/geolocation');
        $geolocationResult = $geolocation->locate($this->getRemoteIp());

        $result = isset($geolocationResult['country']) ? $geolocationResult['country'] : false;

        $session->setData('amgdpr_country', $result);

        return $result;
    }

    public function getRemoteIp()
    {
        $ip = Mage::helper('core/http')->getRemoteAddr();
        $ip = substr($ip, 0, strrpos($ip, ".")) . '.0';

        return $ip;
    }
}