<?php
/**
 * Layer filter decimal block rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    /**
     * @return Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function getFilter()
    {
        return $this->_filter;
    }
}