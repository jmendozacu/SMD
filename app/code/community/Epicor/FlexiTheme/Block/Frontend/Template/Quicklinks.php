<?php

class Epicor_FlexiTheme_Block_Frontend_Template_Quicklinks extends Epicor_Common_Block_Template_Links
{

    public $_origBlock;
    public $_linksSet = false;

     /**
     * Set default template
     *
     */
    protected function _construct()
    {
        if(Mage::getSingleton('core/design_package')->getPackageName() == 'flexitheme'){ 
             $this->setTemplate('page/template/quicklinks.phtml');
        }
    }

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
        $quicklinks = $this->checkIfContractsLinkRequired();
        return parent::_beforeToHtml();
    }

    public function setClone($clone) {
        $this->_origBlock = $clone;
    }
    public function checkIfContractsLinkRequired() {
        //check if the links button is to be displayed for flexitheme 
        $listsHelper = Mage::helper('epicor_lists/frontend_contract');
        if(
                $listsHelper->contractsDisabled() ||
                count($listsHelper->getActiveContracts()) == 0
            ){
            $url = $this->getUrl('lists/contract/select', array('_secure' => true));
            $this->removeLinkByUrl($url);
        }    
        
    }
}
