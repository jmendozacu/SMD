<?php

/**
 * Branch frontend actions
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */
class Epicor_BranchPickup_PickupController extends Mage_Core_Controller_Front_Action
{
    
    
    public function preDispatch()
    {
        parent::preDispatch();
        $helper = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Data */
        if (!$helper->isBranchPickupAvailable()) {
            Mage::getSingleton('customer/session')->addError('Branch Pickup not available');
            $this->_redirect('/');
        }
    }
    
    
    
    /**
     * Sends the Branchp Pickup grid Selector
     *
     * @return void
     */
    public function selectAction()
    {
        $this->_title($this->__('Branch Pickup Select'));
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Sends the Branchp Pickup select grid action
     *
     * @return void
     */
    public function selectgridAction()
    {
        //$this->loadEntity();
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_branchpickup/pickup_select_grid')->toHtml());
    }
    
    /**
     * Sends the Branchp Search grid Selector
     *
     * @return void
     */
    public function pickupsearchAction()
    {
        $this->_title($this->__('Branch Pickup Search'));
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Sends the Branchp Search select grid action
     *
     * @return void
     */
    public function pickupsearchgridAction()
    {
        //$this->loadEntity();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Branch Select Action
     */
    public function selectBranchAction()
    {
        $branch               = $this->getRequest()->getParam('branch');
        $returnUrl            = $this->getRequest()->getParam('return_url');
        $helper               = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */
        $helperBranchLocation = Mage::helper('epicor_branchpickup/branchpickup');
        /* @var  Epicor_BranchPickup_Helper_Branchpickup */
        if ($branch && $helper->isValidLocation($branch)) {
            $helper->selectBranchPickup($branch);
            $helperBranchLocation->setBranchLocationFilter($branch);
        }
        if ($contract == -1) {
            $helper->selectBranchPickup(null);
        }
        if ($returnUrl) {
            $returnUrl = $helper->urlDecode($returnUrl);
            $this->_redirectUrl($returnUrl);
        } else {
            $this->_redirect('/');
        }
    }
    
    
    /**
     * Branch Select Ajax Action
     */
    public function selectBranchAjaxAction()
    {
        $branch               = $this->getRequest()->getParam('branch');
        $helper               = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Data */
        $helperBranchLocation = Mage::helper('epicor_branchpickup/branchpickup');
        /* @var  Epicor_BranchPickup_Helper_Branchpickup */
        if ($branch && $helper->isValidLocation($branch)) {
            $helper->selectBranchPickup($branch);
            $helperBranchLocation->setBranchLocationFilter($branch);
        }
        if ($contract == -1) {
            $helper->selectBranchPickup(null);
        }
        
    }
    /**
     * Change Pickup Action in Checkout Dropdown
     * return json
     */
    
    public function changePickupLocationAction()
    {
        $locationCode  = $this->getRequest()->getParam('locationcode');
        $checkProducts = Mage::getModel('epicor_branchpickup/branchpickup')->pickupValidation($locationCode);
        /* @var  Epicor_BranchPickup_Model_BranchPickup */
    }
    
    /**
     * Remove Branch Pickup action in Grid
     * return null
     */
    
    public function removebranchpickupAction()
    {
        $helper = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Data */
        $helper->selectBranchPickup(null);
        $helper->resetBranchLocationFilter();
        $this->_redirect('*/*/select');
    }
    
    /**
     * Shows the cart popup, If the item is available for the pickup location
     * return html
     */
    
    public function cartPopupAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('checkout/cart_sidebar')->setTemplate('epicor/branchpickup/checkout/sidebar.phtml')->toHtml());
    }
    
    
    /**
     * Remove the items in the cart(After user confirmation), If the item is available for the pickup location
     * return boolean
     */
    
    public function removeItemsInCartAction()
    {
        $postValues    = Mage::App()->getRequest()->getParam('removeitems');
        $branch        = Mage::App()->getRequest()->getParam('branch');
        $separateItems = explode(',', $postValues);
        $cartHelper    = Mage::helper('checkout/cart');
        $items         = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            if (in_array($item->getProduct()->getId(), $separateItems)) {
                $itemId = $item->getItemId();
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
        }
        $helperBranchLocation = Mage::helper('epicor_branchpickup/branchpickup');
        /* @var  Epicor_BranchPickup_Helper_Branchpickup */
        $helper               = Mage::helper('epicor_branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Data */
        if ($branch && $helper->isValidLocation($branch)) {
            $helper->selectBranchPickup($branch);
            $helperBranchLocation->setBranchLocationFilter($branch);
        }
        $this->_redirect('checkout/cart');
    }
    
    /* Save Branch pickup location in checkout page */
    
    public function saveLocationQuoteAction()
    {
        $locationCode         = Mage::App()->getRequest()->getParam('locationcode');
        $helperBranchLocation = Mage::helper('epicor_branchpickup/branchpickup');
        /* @var $helper Epicor_BranchPickup_Helper_Branchpickup */
        $result               = $helperBranchLocation->saveShippingInQuote($locationCode);
        Mage::App()->getResponse()->setHeader('Content-type', 'application/json');
        Mage::App()->getResponse()->setBody(json_encode($result));
    }
    
    public function locationAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    
    public function saveLocationAction()
    {
        if ($dataVals = $this->getRequest()->getPost('data')) {
            parse_str($dataVals, $data);
            if ($data['locationid']) {
                $location = Mage::getModel('epicor_comm/location')->load($data['locationid']);
                /* @var $location Epicor_Comm_Model_Location */
                if (isset($data['county_id'])) {
                    $region              = Mage::getModel('directory/region')->load($data['county_id']);
                    /* @var $region Mage_Directory_Model_Region */
                    $data['county_code'] = $region->getCode();
                }
                $location->addData($data);
                if (!$location->getSource()) {
                    $location->setSource('web');
                    $location->setDummy(0);
                }
                $location->save();
                $result['type'] = 'success';
                $result['data'] = $data;
                Mage::App()->getResponse()->setHeader('Content-type', 'application/json');
                Mage::App()->getResponse()->setBody(json_encode($result));
            }
        }
    }
    
}