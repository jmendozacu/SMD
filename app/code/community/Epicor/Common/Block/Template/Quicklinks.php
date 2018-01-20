<?php

/**
 * 
 * Template link override block
 * 
 *  - adds access check to link display
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team 
 */
class Epicor_Common_Block_Template_Quicklinks extends Epicor_FlexiTheme_Block_Frontend_Template_Quicklinks
{

    protected function _beforeToHtml()
    {
        //storeswitcher variables
        $groups = Mage::app()->getWebsite()->getGroups();
        $currentGroupId = Mage::app()->getStore()->getGroupId();    
        $useStoreSwitcher = Mage::getModel('core/store_group')->load($currentGroupId)->getStoreswitcher();
        $websiteId = Mage::app()->getWebsite()->getId();
        $storesToSelect = Mage::getModel('core/store_group')->getCollection()
                    ->addFieldToFilter('website_id', array('eq'=>$websiteId))
                    ->addFieldToFilter('storeswitcher', array('eq'=>true))
                    ->count();
        
        if ($this->_origBlock) {
            $quicklinks = Mage::app()->getLayout()->getBlock($this->_origBlock);
            if ($quicklinks) {
                $this->_links = $quicklinks->getLinks();
            } 
        }

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        foreach ($this->_links as $x => $link) {
            $url = ($link['url']) ? : '';
            if (!empty($url) && !$helper->canAccessUrl($url)) {
                unset($this->_links[$x]);
            }
            if($link['title']=='brandselect' && (!$useStoreSwitcher || $storesToSelect < 2) ){          // don't display brand select link, if not required
                  unset($this->_links[$x]);
            }
        }

        $this->_linksSet = true;
        parent::_beforeToHtml();
    }

}
