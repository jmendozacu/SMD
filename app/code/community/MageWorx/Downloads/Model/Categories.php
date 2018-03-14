<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Categories extends Mage_Core_Model_Abstract
{
    protected static $_url = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/categories');
    }

    public function getCategoriesList($type = null)
    {
        $layouts = array();
        $categories = $this->getResource()->getAccessCategories();
        if ($categories) {
            foreach ($categories as $value) {
                if (null === $type) {
                    $layouts[$value['category_id']] = (string)$value['title'];
                } else {
                    $layouts[$value['title']] = (string)$value['title'];
                }
            }
        }
        return $layouts;
    }

    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }
}
