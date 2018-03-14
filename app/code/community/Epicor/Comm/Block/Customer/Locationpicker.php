<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Locationpicker
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Customer_Locationpicker extends Mage_Core_Block_Template
{

    protected $_locationHelper;

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Location Picker'));
    }

    /**
     * Get Location Helper
     * @return Epicor_Comm_Helper_Locations
     */
    public function getLocationHelper()
    {
        if (!$this->_locationHelper) {
            $this->_locationHelper = Mage::helper('epicor_comm/locations');
        }
        return $this->_locationHelper;
    }

    public function isAllowed()
    {
        $stockVisibility = Mage::getStoreConfig('epicor_comm_locations/global/stockvisibility');
        $enabled = ($this->getLocationHelper()->isLocationsEnabled()) && ($this->getLocationHelper()->isLocationsPickerEnabled());
        $locationsCount = count($this->getCustomerAllowedLocations());
        
        return $enabled && ($locationsCount > 1 || $this->isGroupSelected()) && $stockVisibility != 'all_source_locations';
    }

    /**
     * Get the customer from the session
     * 
     * @return Epicor_Comm_Model_Customer
     */
    public function getCustomer()
    {
        $session = Mage::getSingleton('customer/session');
        /* @var $session Mage_Customer_Model_Session */
        $customer = $session->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */
        return $customer;
    }

    public function getReturnUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        return Mage::helper('epicor_comm')->urlEncode($url);
    }

    public function getFormUrl()
    {
        return Mage::getUrl('epicor_comm/locations/filter');
    }

    /**
     * Checks config to see if user can choose single or multiple locations
     * 
     * @return boolean
     */
    public function canChooseMultipleLocations()
    {
        return true;
    }

    /**
     * 
     * @param string $code
     * 
     * @return boolean
     */
    public function isLocationSelected($code)
    {
        return in_array($code, $this->getLocationHelper()->getCustomerDisplayLocationCodes());
    }

    /**
     * Get session customer allowed locations
     * 
     * @return array
     */
    public function getCustomerAllowedLocations()
    {
        $locations = $this->getLocationHelper()->getCustomerAllowedLocations();
        if (!is_array($locations)) {
            $locations = array();
        }
        if ($groupId = $this->isGroupSelected()) {
            $locations = Mage::getSingleton('epicor_comm/location_groupings')->getLocations($groupId);
        }
        return $locations;
    }
    
    /**
     * Get All Groups
     * 
     * @return Object
     */
    public function getGroupLocations()
    {
        $groups = Mage::getModel('epicor_comm/location_groupings')->getCollection()
                ->addFieldToSelect('id')
                ->addFieldToSelect('group_name')
                ->addFieldToFilter('enabled', 1)
                ->setOrder('main_table.order', 'ASC');
        return $groups;
    }
    
    /**
     * Get the selected Group at Location Filter
     * 
     * @return int
     */
    public function isGroupSelected($unset = false)
    {
        $session = Mage::getSingleton('customer/session');
        $groupId = $session->getGroupId();
        if ($unset == true) {
            $session->unsGroupId();
        }
        return $groupId;
    }

}
