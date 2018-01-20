<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 11:11
 */

class Webtise_Gallery_Block_View extends Mage_Core_Block_Template
{
    /**
     * Current page for Galleries
     *
     * @var $_page
     */
    protected $_page;

    /**
     * Validate page set in xml
     *
     * @param string $page
     * @return bool
     */
    public function isValidPage($page) {
        $pages = Mage::getSingleton('gallery/source_pages')->getAllOptions();
        $flag = false;
        foreach($pages as $p) {
            if(in_array($page, $p)) {
                $flag = true;
            }
        }
        return $flag;
    }

    /**
     * Set page, if none is set in xml. use current page
     * page is validated before being set
     *
     * @param string $page
     */
    public function setPage($page = null) {
        if(!$page) {
            $helper = Mage::helper('gallery');
            $currentPage = $helper->getCurrentPage();
            if($currentPage && $this->_page !== $currentPage) {
                $this->_page = $currentPage;
            }
        } else {
            if ($this->isValidPage($page)) {
                $this->_page = $page;
            }
        }
    }

    /**
     * Check whether gallery has generic url
     *
     * @param $image
     * @return bool
     */
    public function hasLink($image) {
        $_galleryId = $this->getData('gallery_id');
        $_gallery = Mage::getModel('gallery/gallery')->load($_galleryId);
        $imageLink = $image->getImageSpecificUrl();
        if($_gallery->getIsGenericUrl() == '1' || $imageLink) {
            return true;
        }
        return false;
    }

    public function hasProductLink($image) {
        $productIds = $image->getRelatedProductIds();
        if($productIds) {
            return true;
        }
        return false;
    }

    public function getImageLink($image, $gallery) {
        $productIds = $image->getRelatedProductIds();
        $specificUrl = $image->getImageSpecificUrl();
        if($productIds) {
            return $this->getUrl('gallery', array(
                'products'  => $productIds,
                'gallery'   => $this->getData('gallery_id'),
                'image'     => $image->getId()
            ));
        } elseif($specificUrl) {
            return $specificUrl;
        } elseif($gallery->getIsGenericUrl()) {
            return $gallery->getGenericUrl();
        }
        return $this->getUrl('gallery', array(
            'products'  => $productIds,
            'gallery'   => $this->getData('gallery_id'),
            'image'     => $image->getId()
        ));
    }

    /**
     * Return Gallery Tag from Image if exists
     *
     * @param $image
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getImageTags($image)
    {
        $tags = explode(',', $image->getTagIds());
        if(count($tags) > 0) {
            return Mage::getModel('gallery/gallery_tag')->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('entity_id', array('in' => $tags));
        }
        return false;
    }

    /**
     * Return comma separated string of ids
     *
     * @param $image
     * @return string
     */
    public function getTagIds($image)
    {
        return implode(',', $this->getImageTags($image)->getAllIds());
    }

}