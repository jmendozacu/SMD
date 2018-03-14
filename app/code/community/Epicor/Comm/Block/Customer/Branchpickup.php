<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Branchpicker
 *
 */
class Epicor_Comm_Block_Customer_Branchpickup extends Mage_Core_Block_Template {

    public function _construct() {
        parent::_construct();
        $this->setTitle($this->__('Selected Branch'));
    }
    
    /**
     * Checks if Message Block For Branch Picker is allowed in the config.
     * @return boolean
     */
    public function isBlockAllowed() {
        $branchHelper = Mage::helper('epicor_branchpickup');
        $branchpickupEnabled = $branchHelper->isBranchPickupAvailable();
        $showbranchpickupblock = Mage::getStoreConfig('epicor_comm_locations/global/showbranchpickupblock', Mage::app()->getStore());
        if ($branchpickupEnabled && in_array($showbranchpickupblock, array(2, 3))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the selected branch details from session
     * @return selected location code
     */
    public function getSelectedBranch() {
        $branchHelper = Mage::helper('epicor_branchpickup');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
        $getSelectedBranch = $branchHelper->getSelectedBranch();
        return $getSelectedBranch;
    }

    public function getBranches() {
        $locationIds = $this->_getSelected();
        $collection = Mage::getModel('epicor_comm/location')->getCollection();
        $collection->addFieldToFilter('code', array(
            'in' => $locationIds
        ));
        $collection->getSelect()->order('sort_order ASC');
        return $collection;
    }

    public function _getSelected() {
        $helperbranch = Mage::helper('epicor_branchpickup');
        /* @var Epicor_BranchPickup_Helper_Data */
        return array_keys($helperbranch->getSelected());
    }

    public function getFormUrl() {
        return '';
    }
}