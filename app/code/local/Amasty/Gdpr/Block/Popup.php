<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Popup extends Mage_Core_Block_Template
{
    public function getText()
    {
        $modelContent = Mage::getModel('amgdpr/privacyPolicy')->getCurrentPolicy();
        return $modelContent->getContent();
    }
}
