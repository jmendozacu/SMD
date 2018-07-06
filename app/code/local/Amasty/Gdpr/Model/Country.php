<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Country
{
    public function isEEACountry($countryCode)
    {
        $eeaCountries = explode(',', Mage::getStoreConfig('amgdpr/eea_countries'));

        return in_array($countryCode, $eeaCountries);
    }
}
