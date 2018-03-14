<?php

class Epicor_Comm_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{

    public function getTotalNum()
    {
        if (!Mage::getStoreConfigFlag('cataloginventory/options/show_out_of_stock')) {
            return $this->getCollection()->count();
        } else {
            return $this->getCollection()->getSize();
        }
    }

}
