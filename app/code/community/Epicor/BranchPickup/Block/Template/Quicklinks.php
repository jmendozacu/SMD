<?php

class Epicor_BranchPickup_Block_Template_Quicklinks extends Epicor_FlexiTheme_Block_Frontend_Template_Quicklinks
{
    
    public $_origBlock;
    public $_linksSet = false;
    
    
    /**
     * Get base data from default quick-access Menu
     *
     * @return Mage_Page_Block_Template_Links
     */
    protected function _beforeToHtml()
    {
        if ($this->_origBlock && !$this->_linksSet) {
            $quicklinks = Mage::app()->getLayout()->getBlock($this->_origBlock);
            if ($quicklinks)
                $this->_links = $quicklinks->getLinks();
        }
        //this is required because the observer has a zero value for teh $this->_links variable when it is hit
        $branchPickup    = $this->checkIfBranchPickupLinkRequired();
        $this->_linksSet = true;
        return parent::_beforeToHtml();
    }
    
    
    public function checkIfBranchPickupLinkRequired()
    {
        //check if the links button is to be displayed for flexitheme 
        $branchHelper        = Mage::helper('epicor_branchpickup');
        $branchpickupEnabled = $branchHelper->isBranchPickupAvailable();
        //$isLoggedIn          = Mage::helper('customer')->isLoggedIn();
        $helperBranchPickup  = Mage::helper('epicor_branchpickup/branchpickup');
        
        if (!$branchpickupEnabled) {
            $url = $this->getUrl('branchpickup/pickup/select', $helperBranchPickup->issecure());
            $this->removeLinkByUrl($url);
        }
    }
    
}