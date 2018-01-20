<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 24/06/2016
 * Time: 15:46
 */

class Webtise_Gallery_Block_Products extends Mage_Core_Block_Template
{
    private $_products;
    private $_gallery;
    private $_image;

    /**
     * Set product collection for block
     *
     * @param $ids
     */
    public function setProducts($ids) {
        $productIds = explode(',', $ids);
        $products = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect(array('*'))
            ->addIdFilter($productIds)
            ->addUrlRewrite();

        $backendModel = $products->getResource()->getAttribute('media_gallery')->getBackend();

        foreach($products as $product){
            $backendModel->afterLoad($product);
        }

        $this->_products = $products;
    }

    /**
     * Load Gallery to be used in block
     *
     * @param $gallery
     */
    public function setGallery($gallery) {
        $this->_gallery = Mage::getModel('gallery/gallery')->load($gallery);
    }

    /**
     * Set Image Id for block to grab selected image
     *
     * @param $image
     */
    public function setImage($image) {
        $this->_image = $image;
    }

    /**
     * Return Products if set
     *
     * @return bool | object
     */
    public function getProducts() {
        if($this->_products) {
            return $this->_products;
        }
        return false;
    }

    /**
     * Return gallery if set
     *
     * @return bool | object
     */
    public function getGallery() {
        if($this->_gallery) {
            return $this->_gallery;
        }
        return false;
    }

    /**
     * Return Image Id if set
     *
     * @return bool | string
     */
    public function getImage() {
        if($this->_image) {
            return $this->_image;
        }
        return false;
    }

    /**
     * Get Selected image using image ID set in controller
     *
     * @return bool | object
     */
    public function getSelectedImage() {
        if($this->_image && $this->_gallery) {
            $gallery = $this->_gallery;
            $images = $gallery->getMediaGalleryImages();
            foreach($images as $image) {
                if($image->getId() == $this->_image) {
                    $selected = $image;
                }
            }
            if(isset($selected)) {
                return $selected;
            }
        }
        return false;
    }

    public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }

    public function getAddToCartUrl($productId, $additional = array()) {
        $continueUrl    = Mage::helper('core')->urlEncode($this->getCurrentUrl());
        $urlParamName   = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $query = array(
            'product'   => $productId
        );

        $routeParams = array(
            $urlParamName   => $continueUrl,
            'product'       => $productId,
            'form_key'      => Mage::getSingleton('core/session')->getFormKey(),
            '_query'        => $query
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        return $this->getUrl('checkout/cart/add', $routeParams);
    }
}
