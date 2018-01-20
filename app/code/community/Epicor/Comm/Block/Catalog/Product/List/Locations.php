<?php

/**
 * Locations 
 * 
 * Displays Locations on the product list page
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Catalog_Product_List_Locations extends Epicor_Comm_Block_Catalog_Product_Locations
{

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Gets the list mode
     * 
     * @return string
     */
    public function getListMode()
    {
        return Mage::registry('list_mode');
    }

    public function getProductUrl()
    {
        return $this->getParentBlock()->getProductUrl();
    }

    public function resetProduct()
    {
        $this->getProduct()->restoreOrigData();
    }

}
