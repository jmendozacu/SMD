<?php

/**
 * Locations 
 * 
 * Displays Locations on the product list/view page
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Catalog_Product_Locations extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Returns the current product
     * 
     * @return Epicor_Comm_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Gets the list mode
     * 
     * @return string
     */
    public function getListMode()
    {
        return Mage::registry('list_mode');
    }

    /**
     * Returns product price block html
     *
     * @param Epicor_Comm_Model_Location_Product $location
     * @param Epicor_Comm_Model_Product $product
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getPriceHtml($location, $product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $product->getTypeId();

        $product->setToLocationPrices($location);

        $parent = $this->getParentBlock();

        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $parent->_preparePriceRenderer($type_id)
                    ->setProduct($product)
                    ->setDisplayMinimalPrice($displayMinimalPrice)
                    ->setIdSuffix($idSuffix)
                    ->toHtml();
            $product->setAddToCartUrl($parent->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $parent->_preparePriceRenderer($type_id)
                        ->setProduct($product)
                        ->setDisplayMinimalPrice($displayMinimalPrice)
                        ->setIdSuffix($idSuffix)
                        ->toHtml();
    }

    public function getAddToCartUrl($product)
    {
        return $this->getParentBlock()->getAddToCartUrl($product);
    }

    public function getReturnUrl()
    {
        return $this->getParentBlock()->getReturnUrl();
    }

}
