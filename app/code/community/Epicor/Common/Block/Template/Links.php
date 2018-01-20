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
class Epicor_Common_Block_Template_Links extends Mage_Page_Block_Template_Links
{

    public function setLinks($links)
    {
        $this->_links = $links;
    }

    protected function _beforeToHtml() {
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        $siteOfflineDontDisplayCheckout = (!Mage::getStoreConfig('Epicor_Comm/xmlMessaging/failed_msg_online') &&
                                                Mage::getStoreConfig('Epicor_Comm/xmlMessaging/site_offline_checkout_disabled')) ? true : false;
        foreach ($this->_links as $x => $link) {
            if ($link['title'] == 'Checkout' && $siteOfflineDontDisplayCheckout) {
                unset($this->_links[$x]);
                continue;
            }
            $url = ($link['url']) ? : '';
            if (!empty($url) && !$helper->canAccessUrl($url)) {
                unset($this->_links[$x]);
                continue;
            }
        }
        parent::_beforeToHtml();
    }

}
