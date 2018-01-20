<?php

/**
 * 
 * Customer Account Navigation block
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Customer_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    /* @var $exclude_links for hiding account menu options */
    private $exclude_links = null;
    
    public function addLink($name, $path, $label, $urlParams = array(), $msgtype = null, $accessFunction = null)
    {

        // check if message is enabled before adding to link 
        if ($msgtype) {
            $msgAvailable = Mage::helper('epicor_comm')->checkMsgAvailable(strtoupper($msgtype));
            if (!$msgAvailable) {
                return;
            }
        }
        
        if ($accessFunction) {
            $function = explode('::', $accessFunction);
            $helper = Mage::helper($function[0]);
            if (!$helper->$function[1]()) {
                return;
            }
        }
        
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        $allowed = $helper->canAccessUrl($path, false, true);
        $this->exclude_links = explode(',',Mage::getStoreConfig('customer/account_menu_options/menu_custom_disallowed'));
        
        if ($allowed) {
            if (!in_array($name, $this->exclude_links)) {
                parent::addLink($name, $path, $label, $urlParams);
            }
        }
    }

    public function _toHtml()
    {
        if (count($this->_links) == 0) {
            $html = '';
        } else {
            $html = parent::_toHtml();
        }
        return $html;
    }
    
    public function removeLinkByName($name) {
        unset($this->_links[$name]);
    }

}
