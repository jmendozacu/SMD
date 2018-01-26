<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:00
 */

class Webtise_Gallery_Model_Gallery_Tag extends Mage_Core_Model_Abstract
{
    const ENTITY    = 'gallery_gallery_tag';
    const CACHE_TAG = 'gallery_gallery_tag';
    protected $_eventPrefix = 'gallery_gallery_tag';
    protected $_eventObject = 'gallery_tag';
    protected $_categoryInstance = null;

    public function _construct() {
        parent::_construct();
        $this->_init('gallery/gallery_tag');
    }

    public function getDescription() {
        $description = $this->getData('description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($description);
        return $html;
    }

    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        return $values;
    }
}