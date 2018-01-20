<?php

/**
 * 
 * Wishlist link override block
 * 
 *  - adds access check to wishlist link display
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Template_Links_Wishlist extends Mage_Wishlist_Block_Links
{

    protected function _toHtml()
    {
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $html = parent::_toHtml();
        if (!$helper->canAccessUrl($this->_url)) {
            $html = '';
        }
        return $html;
    }

}
