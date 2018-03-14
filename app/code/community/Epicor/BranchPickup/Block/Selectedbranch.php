<?php
/**
 * Branch Pickup Block
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */

class Epicor_BranchPickup_Block_Selectedbranch extends Epicor_Comm_Block_Catalog_Product_List_Locations
{
    
    public function __construct()
    {
        parent::__construct();
        $helpers = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Data */
        
        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        
        $style = $helpers->getLocationStyle();
        if($locHelper->isLocationsEnabled() && $style == 'inventory_view' && !$helpers->getSelectedBranch()) {
            $helperBranchPickup = Mage::helper('epicor_branchpickup/branchpickup');
            /* @var $helper Epicor_BranchPickup_Helper_Branchpickup */
            $locationHelper = Mage::helper('epicor_comm/locations');
            /* @var $helper Epicor_Comm_Helper_Locations */
            $defaultLocationCode = $locationHelper->getDefaultLocationCode();
            $sessionHelper = Mage::helper('epicor_lists/session');
            /* @var $sessionHelper Epicor_Lists_Helper_Session */
            $sessionHelper->setValue('ecc_selected_branchpickup', $defaultLocationCode);
            $helpers->selectBranchPickup($defaultLocationCode);
            $helperBranchPickup->setBranchLocationFilter($defaultLocationCode);
        } else if ($style == 'location_view' && !$helpers->isBranchPickupAvailable()  && $helpers->getSelectedBranch()) {
            $sessionHelper = Mage::helper('epicor_lists/session');
            /* @var $sessionHelper Epicor_Lists_Helper_Session */
            $sessionHelper->setValue('ecc_selected_branchpickup',"");
            $helpers->resetBranchLocationFilter();
        }
        $selectedBranch = $helpers->getSelectedBranch();
        $allowed = array_keys($locHelper->getCustomerAllowedLocations());
        if ($selectedBranch &&
            (!$locHelper->getLocation($selectedBranch)->getLocationVisible() || !in_array($selectedBranch, $allowed))) {
            $error = $locHelper->__('Access to Site is blocked, as selected branch is not valid');
            if (($locHelper->errorExists('customer/session', $error) == false) &&
                Mage::app()->getRequest()->getControllerName() != 'portal') {
                Mage::getSingleton('customer/session')->addError($error);
            }
            if (!Mage::getSingleton('customer/session')->getIsNotified() &&
                Mage::app()->getRequest()->getControllerName() != 'portal') {
                Mage::getSingleton('customer/session')->setIsNotified(true);
                $erpAccount = $locHelper->getErpAccountInfo();
                $title = $locHelper->__("Inventory View Issue:");
                $message = $locHelper->__("Please set valid location for %s.", $erpAccount->getName());
                $locHelper->sendMagentoMessage($message, $title, Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE, $link = null);
            }
        }
    }
    /**
     * Returns selected branch
     * 
     * @param type $locationCode
     * @return Epicor_Comm_Model_Location
     */
    public function getSelectedBranch($locationCode)
    {
        return Mage::getModel('epicor_comm/location')->load($locationCode, 'code');
    }
    
    /**
     * 
     * @param int $locationId
     * @return Epicor_Comm_Model_Mysql4_Location_Collection
     */
    public function getRelatedLocations($locationId)
    {
        $helper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        $allowed = array_keys($helper->getCustomerAllowedLocations());
        $relatedLocations = Mage::getModel('epicor_comm/location_relatedlocations')->getRelatedLocations($locationId);
        $relatedLocations->addFieldToFilter('main_table.location_visible', 1)
                        ->addFieldToFilter('main_table.code', array('in' => $allowed));
        return $relatedLocations;
    }
    
    /**
     * Returns address of the branch
     * 
     * @param Epicor_Comm_Model_Location $location
     * @return string
     */
    public function getBranchAddress($location)
    {
        $address = "";
        $address .= ($location->getAddress1()) ? $location->getAddress1().", " : ""; 
        $address .= ($location->getAddress2()) ? $location->getAddress2().", " : ""; 
        $address .= ($location->getAddress3()) ? $location->getAddress3().", " : ""; 
        $address .= ($location->getCity()) ? $location->getCity().", " : ""; 
        $address .= ($location->getCounty()) ? $location->getCounty().", " : ""; 
        $address .= ($location->getCountry()) ? $location->getCountry().", " : ""; 
        $address .= ($location->getPostcode()) ? $location->getPostcode().", " : ""; 
        return rtrim($address,", ");
    }
    
    /**
     * Gets the related locations details for product
     * 
     * @return array
     */
    public function getRelatedLocationsForProduct()
    {
        $_product = $this->getProduct();
        $branchpickupHelper = Mage::helper('epicor_branchpickup');
        $selectedBranch = $branchpickupHelper->getSelectedBranch();
        if (!Mage::registry('related_locations')) {
            $_relatedLocations = Mage::getModel('epicor_comm/location_relatedlocations')->getRelatedLocationsForProduct($selectedBranch);
            Mage::unregister('related_locations');
            Mage::register('related_locations', $_relatedLocations);
        }
        $_relatedLocations = Mage::registry('related_locations');
        $_locations = $_product->getLocations();
        $helper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        $allowed = $helper->getCustomerAllowedLocations();
        $locations = array_intersect_key($_locations, $allowed);
        $relatedLocations = array();
        
        $locVisibilityCount = $showInventoryCount = 0;
        foreach ($locations as $location) {
            /* @var $location Epicor_Comm_Model_Location_Product */
            if (in_array(strval($location->getLocationCode()), $_relatedLocations)) {
                $includeInventory = $_relatedLocations[strval($location->getLocationCode())]['include_inventory'];
                $showInventory = $_relatedLocations[strval($location->getLocationCode())]['show_inventory'];
                $locationVisible = $_relatedLocations[strval($location->getLocationCode())]['location_visible'];
                $location->setIncludeInventory($includeInventory);
                $location->setShowInventory($showInventory);
                if($includeInventory) {
                    $relatedLocations[strval($location->getLocationCode())] = $location;
                } else {
                    $relatedLocations[] = $location;
                }
                if ($locationVisible) {
                    $locVisibilityCount++;
                }
                $showInventoryCount += $showInventory;
            }
        }
        Mage::register('rellocation_visibility_count_'.$_product->getId(), $locVisibilityCount);
        Mage::register('rellocation_showinventory_count_'.$_product->getId(), $showInventoryCount);
        return $relatedLocations;
    }
    
    /**
     * Returns the Location Grouping for product
     * 
     * @return array
     */
    public function getGroupings()
    {
        $_product = $this->getProduct();
        $branchpickupHelper = Mage::helper('epicor_branchpickup');
        $selectedBranch = $branchpickupHelper->getSelectedBranch();
        $_locations = $_product->getLocations();
        $helper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        $allowed = $helper->getCustomerAllowedLocations();
        $locations = array_intersect_key($_locations, $allowed);
        if (!Mage::registry('branch_groupings')) {
            $groups = Mage::getModel('epicor_comm/location_groupings')->getGroupLocations($selectedBranch);
            Mage::unregister('branch_groupings');
            Mage::register('branch_groupings', $groups);
        }
        $groups = Mage::registry('branch_groupings');
        $_groups = array();
        foreach ($groups as $groupName => $group) {
            $_groups[$groupName]['group_id'] = $group['group_id'];
            $_groups[$groupName]['group_expandable'] = $group['group_expandable'];
            $_groups[$groupName]['show_aggregate_stock'] = $group['show_aggregate_stock'];
            $_groups[$groupName]['location_visibility_count'] = $_groups[$groupName]['location_showinventory_count'] = 0;
            foreach ($locations as $location) {
                if (in_array(strval($location->getLocationCode()), $group['locations'])) {
                    $includeInventory = $group['locations'][strval($location->getLocationCode())]['include_inventory'];
                    $showInventory = $group['locations'][strval($location->getLocationCode())]['show_inventory'];
                    $locationVisibile = $group['locations'][strval($location->getLocationCode())]['location_visible'];
                    $location->setIncludeInventory($includeInventory);
                    $location->setShowInventory($showInventory);
                    if ($includeInventory) {
                        $_groups[$groupName]['locations'][strval($location->getLocationCode())] = $location;
                    } else {
                        $_groups[$groupName]['locations'][] = $location;
                    }
                    if ($locationVisibile) {
                        $_groups[$groupName]['location_visibility_count']++;
                    }
                    $_groups[$groupName]['location_showinventory_count'] += $showInventory;
                }
            }
        }
        return $_groups;
    }
    
    /**
     * Get Group Locations for the product
     * 
     * @return Object
     */
    public function getGroupLocations()
    {
        $helper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        $allowed = array_keys($helper->getCustomerAllowedLocations());
        $branchpickupHelper = Mage::helper('epicor_branchpickup');
        $selectedBranch = $branchpickupHelper->getSelectedBranch();
        $groups = Mage::getModel('epicor_comm/location_groupings')->getGroupings($selectedBranch);
        $groups->addFieldToFilter('locations.code', array('in' =>$allowed));
        return $groups;     
    }
    
    public function getAllLocations()
    {
        $helperbranch = Mage::helper('epicor_branchpickup');
        /* @var Epicor_BranchPickup_Helper_Data */
        $locationIds = array_keys($helperbranch->getSelected());
        $collection  = Mage::getModel('epicor_comm/location')->getCollection()
                        ->addFieldToFilter('code', array(
                        'in' => $locationIds
                        ))
                    ->addFieldToFilter('location_visible', 1);
        $collection->getSelect()->order('sort_order ASC');
        return $collection;
    }
}