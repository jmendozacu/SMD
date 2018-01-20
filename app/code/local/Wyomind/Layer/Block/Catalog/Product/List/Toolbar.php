<?php
/**
 * Product list toolbar block rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
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