<?php

class Epicor_QuickOrderPad_Block_Form_Search extends Mage_Core_Block_Template
{
    private $_url = '/quickorderpad/form/results';
        
    public function getQuery()
    {
        return Mage::registry('search-query');
    }

    public function getInstock()
    {
        return Mage::registry('search-instock');
    }

    public function getSearchUrl()
    {
        return $this->_url;
    }

    public function setSearchUrl($url)
    {
        $this->_url = $url;
    }
    
    public function showOnlyInStockTickbox()
    {
        $showOnlyInTickbox = false;
        if(!Mage::getStoreConfigFlag('epicor_comm_enabled_messages/msq_request/products_always_in_stock')){            
            if(class_exists('Epicor_Search_Model_Mysql4_Fulltext')){
                $showOnlyInTickbox = true;
            }
        }
        return $showOnlyInTickbox;
    }

}
