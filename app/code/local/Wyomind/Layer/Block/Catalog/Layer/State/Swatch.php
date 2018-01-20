<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_State_Swatch extends Mage_ConfigurableSwatches_Block_Catalog_Layer_State_Swatch
{
    /**
     * @param Mage_Catalog_Model_Layer_Filter_Item $filter
     */
    protected function _init($filter)
    {
        parent::_init($filter);
        $this->_initDone = false; // Disable this flag so multiple filtering is allowed on swatches
    }
}