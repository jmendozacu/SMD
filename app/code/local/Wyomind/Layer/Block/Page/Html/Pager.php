<?php
/**
 * Pager block rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager
{
    /**
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        return Mage::helper('layer')->buildUrl($params);
    }
}