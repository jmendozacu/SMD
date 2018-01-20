<?php

/**
 * Locations view block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Catalog_Product_View_Type_Configurable_Locations extends Mage_Catalog_Block_Product_View_Type_Configurable
{

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Returns product tier price block html
     *
     * @param null|Mage_Catalog_Model_Product $product
     * @param null|Mage_Catalog_Model_Product $parent
     * @return string
     */
    public function getTierPriceHtml($location, $product = null, $parent = null)
    {
        $product->setToLocationPrices($location);

        if (is_null($product)) {
            $product = $this->getProduct();
        }

        return $this->_getPriceBlock($product->getTypeId())
                        ->setTemplate($this->getTierPriceTemplate())
                        ->setProduct($product)
                        ->setInGrouped($product->isGrouped())
                        ->setParent($parent)
                        ->callParentToHtml();
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

        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                    ->setProduct($product)
                    ->setDisplayMinimalPrice($displayMinimalPrice)
                    ->setIdSuffix($idSuffix)
                    ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($type_id)
                        ->setProduct($product)
                        ->setDisplayMinimalPrice($displayMinimalPrice)
                        ->setIdSuffix($idSuffix)
                        ->toHtml();
    }
    
    public function getLocations($product)
    {
        $locations = $product->getCustomerLocations();

        return $this->filterLocations($locations);
    }

    public function filterLocations($locations)
    {
        $filtered = $locations;

        $cartItem = $this->getCartItem();

        if ($cartItem) {
            foreach ($locations as $x => $location) {
                if ($cartItem->getEccLocationCode() != $location->getLocationCode()) {
                    unset($filtered[$x]);
                }
            }
        }

        return $filtered;
    }

    /**
     * 
     * @return Mage_Sales_Model_Quote_Item
     */
    protected function getCartItem()
    {
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();
        $cartItem = false;

        if ($controller == 'cart' && $action == 'configure') {

            $itemId = $this->getRequest()->getParam('id');

            if ($itemId) {
                $cart = Mage::getSingleton('checkout/cart');
                /* @var $cart Mage_Checkout_Model_Cart */

                $cartItem = $cart->getQuote()->getItemById($itemId);
                /* @var $cartItem Mage_Sales_Model_Quote_Item */
            }
        }

        return $cartItem;
    }
    
    
    /**
     * Check whether the price can be shown for the specified product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function getCanShowProductPrice($product)
    {
        return true;//$product->getCanShowPrice() !== false;
    }

}
