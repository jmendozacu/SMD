<?php

/**
 * Cart item comment
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Checkout_Cart_Item_Comment extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_comm/checkout/cart/item/comment.phtml');
    }

    public function getItem() {
        return $this->getParentBlock()->getItem();
    }
    
    public function isCommentAllowed()
    {
        $allowed = Mage::getStoreConfigFlag('checkout/options/line_comments_enabled');

        if ($allowed) {
            $urlString = Mage::helper('core/url')->getCurrentUrl();
            $types = explode(',', Mage::getStoreConfig('checkout/options/show_line_comments'));
            if (strpos($urlString, 'cart')) {
                $type = 'cart';
            } else {
                $type = 'review';
            }

            $allowed = in_array($type, $types);
        }

        if ($allowed) {
            $productAllowed = $this->getItem()->getProduct()->getLineCommentsEnabled();

            if (!$productAllowed) {
                $allowed = false;
            }
        }

        return $allowed;
    }

    public function getCommentLimit()
    {
        $limit = 0;

        $limited = Mage::getStoreConfigFlag('checkout/options/line_comments_limited');
        if ($limited) {
            $limit = Mage::getStoreConfig('checkout/options/max_line_comment_length');
        }
        return $limit;
    }

}
