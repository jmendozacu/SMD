<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 14:33
 */ 
class Webtise_Gallery_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Current page for Galleries
     *
     * @var $_page
     */
    protected $_page;

    /**
     * Current Category ID if page is category
     *
     * @var null
     */
    protected $_categoryId;

    public function convertOptions($options) {

        $converted = array();
        foreach ($options as $option){
            if (isset($option['value']) && !is_array($option['value']) && isset($option['label']) && !is_array($option['label'])){
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    /**
     * Log gallery specific exceptions to gallery-exception.log
     * @param $msg string
     */
    public function logException($msg) {
        Mage::getModel('core/log_adapter', 'gallery-exception.log')->log($msg);
    }

    /**
     * Get Current page code relative to page source model
     * used for galleries
     *
     * @return bool|string
     */
    public function getCurrentPage() {
        $page = Mage::getSingleton('cms/page');
        if ($page->getId()) {
            $this->_page = $page->getIdentifier();
            return $page->getIdentifier();
        }
        $product = Mage::registry('current_product');
        $category = Mage::registry('current_category');
        if ($product && $product->getId()) {
            if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'catalog_product_view') {
                $this->_page = 'product_view';
                return 'product_view';
            }
        } elseif ($category && $category->getId()) {
            if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'catalog_category_view') {
                if($category->getIsAnchor()) {
                    $this->_page = 'category_anchor';
                    return 'category_anchor';
                } else {
                    $this->_page = 'category_non_anchor';
                    return 'category_non_anchor';
                }
            }
        }
        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'checkout_cart_index') {
            $this->_page = 'basket';
            return 'basket';
        }
        return false;
    }

    /**
     * Get available Galleries for current page
     *
     * @return bool
     */
    public function getGalleryCollection() {
        $this->getCurrentPage();
        if(!$this->_page) {
            return false;
        } else {
            $galleries = Mage::getModel('gallery/gallery')->getCollection()
                ->addAttributeToSelect(array(
                    'pages',
                    'show_on_cms',
                    'cms_type',
                    'show_on_categories',
                    'category_type',
                    'category_ids',
                    'product_ids',
                    'sort_order'
                ))
                ->addAttributeToFilter('status', 1)
                ->setOrder('sort_order', 'ASC');
            $ids = array();
            foreach($galleries as $gallery) {
                if($this->canShowOnCms($gallery)) {
                    $ids[] = $gallery->getId();
                } elseif($this->canShowOnCategory($gallery)) {
                    $ids[] = $gallery->getId();
                } elseif($this->canShowOnProduct($gallery)) {
                    $ids[] = $gallery->getId();
                }
            }
            if(!empty($ids)) {
                $availableGalleries = Mage::getModel('gallery/gallery')->getCollection()
                    ->addAttributeToSelect(array('*'))
                    ->addAttributeToFilter('entity_id', array('in' => $ids))
                    ->addAttributeToFilter('status', 1);
                return $availableGalleries;
            }
        }
        return false;
    }

    public function canShowOnCms($gallery) {
        if($gallery->getShowOnCms() == '1') {
            $type = $gallery->getCmsType();
            $cms = Mage::getModel('cms/page')->getCollection()
                ->addFieldToSelect(array('identifier'));
            $pages = array();
            foreach($cms as $page) {
                $pages[] = $page->getIdentifier();
            }
            switch ($type) {
                case 'all':
                    if(in_array($this->_page, $pages)) {
                        return true;
                    }
                    return false;
                    break;
                case 'specific_page':
                    $galleryPages = explode(',', $gallery->getPages());
                    if(in_array($this->_page, $galleryPages)) {
                        return true;
                    }
                    return false;
                    break;
            }
        }
        return false;
    }

    public function canShowOnCategory($gallery) {
        if($gallery->getShowOnCategories() == '1') {
            $category = Mage::registry('current_category');
            if($category) {
                $type = $gallery->getCategoryType();
                switch ($type) {
                    case 'all':
                        return true;
                        break;
                    case 'category_anchor':
                        if($this->_page == 'category_anchor') {
                            return true;
                        }
                        break;
                    case 'category_non_anchor':
                        if($this->_page == 'category_non_anchor') {
                            return true;
                        }
                        break;
                    case 'specific_category':
                        $galleryCats = explode(',', $gallery->getCategoryIds());
                        if(in_array($category->getId(), $galleryCats)) {
                            return true;
                        }
                        break;
                }
            }
        }
        return false;
    }

    public function canShowOnProduct($gallery) {
        if($this->_page == 'product_view') {
            $product = Mage::registry('current_product');
            if($product && $product->getId()){
                $galleryProducts = explode(',', $gallery->getProductIds());
                if (in_array($product->getId(), $galleryProducts)) {
                    return true;
                }
             }
        }
        return false;
    }

    public function normalizeString($string)
    {
        if (function_exists('mb_strtolower')) {
            return trim(mb_strtolower($string, 'UTF-8'));
        }
        return trim(strtolower($string));
    }
}