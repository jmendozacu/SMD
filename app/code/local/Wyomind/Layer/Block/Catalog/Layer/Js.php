<?php
/**
 * Layer Javascript block.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_Js extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getAjaxEnabled()
    {
        return Mage::getStoreConfigFlag('layer/general/enable_ajax') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getAutoScrollEnabled()
    {
        return Mage::getStoreConfigFlag('layer/general/enable_auto_scroll') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getAjaxToolbarEnabled()
    {
        return Mage::getStoreConfigFlag('layer/general/enable_ajax_toolbar') ? 'true' : 'false';
    }

    /**
     * @return array
     */
    public function getJsPriceFormat()
    {
        $format = Mage::app()->getLocale()->getJsPriceFormat();
        $format['requiredPrecision'] = 0;

        return $format;
    }
}