<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 24/06/2016
 * Time: 14:36
 */

class Webtise_Gallery_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize function to set properties of Block used for Modals
     */
    public function _initProductsModal() {
        $products = $this->getRequest()->getParam('products');
        $gallery = $this->getRequest()->getParam('gallery');
        $image = $this->getRequest()->getParam('image');
        $block = $this->getLayout()->getBlock('gallery.products');

        $block->setProducts($products);
        $block->setGallery($gallery);
        $block->setImage($image);
    }

    /**
     * Controller index action
     * loads layout
     * initializes product modal
     * renders layout
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_initProductsModal();
        $this->renderLayout();
    }
}