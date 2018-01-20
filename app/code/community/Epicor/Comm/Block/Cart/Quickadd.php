<?php

/**
 * Quick add block
 * 
 * Displays the quick add to Basket / wishlist block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Cart_Quickadd extends Mage_Core_Block_Template {

    public function _construct() {
        parent::_construct();
        $this->setTitle($this->__('Quick add Product'));
    }

    /**
     * Checks to see if the autocomplete is allowed
     */
    public function autocompleteAllowed() {
        return Mage::getStoreConfigFlag('quickadd/autocomplete_enabled');
    }
    public function showLocations() {       
        // check if locations enabled and loations are to be displayed
        $showLocations = $this->helper('epicor_comm/locations')->isLocationsEnabled();
        if($showLocations){
            $stockVisibility = Mage::getStoreConfig('epicor_comm_locations/global/stockvisibility');
            if(in_array($stockVisibility, (array('all_source_locations', 'default')))){                 // if default location code required         
                $showLocations = false;
            }
        }    
        return $showLocations;
    }
    
}