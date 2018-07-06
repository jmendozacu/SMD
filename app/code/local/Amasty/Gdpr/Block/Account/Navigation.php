<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    public function addLink($name, $path, $label, $urlParams = array())
    {
        parent::addLink($name, $path, $label);
        if ($name == 'account_edit') {
            parent::addLink('policy_popup', 'amgdpr/customer/settings', 'Privacy Settings');
        }
    }
}
