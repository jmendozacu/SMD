<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Child
 *
 * @author Paul.Ketelle
 */
class Epicor_QuickOrderPad_Block_Catalog_Product_List_Child extends Mage_Core_Block_Template
{
    
    protected $_processedInstock = false;
    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

//        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
//        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
//        
//        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
    public function getReturnUrl()
    {
        return Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
    }
    
    
    /**
     * Get Locations Helper
     * @return Epicor_Comm_Helper_Locations
     */
    public function getHelper($type = null)
    {
        return $this->helper('epicor_comm/locations');
    }
    /**
     * Gets an array of UOM products from a UOM product
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return array
     */
    public function getUOMProducts($product)
    {
        $result = $product->getTypeInstance(true)
            ->getAssociatedProducts($product);

        $storeId = $product->getStoreId();
        foreach ($result as $item) {
            $item->setStoreId($storeId);
        }
        
        return $result;
    }

    /**
     * Get if the stock column can be shown on the quick order pad search results
     *
     * @return bool
     */
    public function showStockLevelDisplay(){
        return Mage::getStoreConfig('Epicor_Comm/stock_level/display') != '';
    }
    
    public function showProductImageDisplay(){ 
        return Mage::getStoreConfigFlag('quickorderpad/general/show_quickorderpad_images');
    }
    
    public function productHasOptions($product, $required = false)
    {
        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */
        
        return $helper->productHasCustomOptions($product, $required);
    }
    
    public function getQtyFieldName($product, $child, $productLocation)
    {
        $qtyFieldName = 'qty';
        if ($product->getTypeId() == 'grouped') {
            if ($this->getHelper()->isLocationsEnabled()) {
                $qtyFieldName = 'super_group_locations[' . $productLocation->getLocationCode() . '][' . $child->getId() . ']';
            } else {
                $qtyFieldName = 'super_group[' . $child->getId() . ']';
            }
        }
        
        return $qtyFieldName;
    }

    public function addHiddenLocationCode($product)
    {
        return $this->getHelper()->isLocationsEnabled() && $product->getTypeId() != 'grouped';
    }

}
