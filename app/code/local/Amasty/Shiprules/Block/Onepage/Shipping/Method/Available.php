<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
class Amasty_Shiprules_Block_Onepage_Shipping_Method_Available extends  Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function getShippingPrice($price, $flag)
    {
        return Mage::helper('amshiprules')->getShippingPrice($this, $price, $flag);
    }
}