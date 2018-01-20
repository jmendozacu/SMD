<?php

/**
 * Locations view block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Catalog_Product_View_Locations extends Epicor_Comm_Block_Catalog_Product_Locations
{

    public function _construct()
    {
        parent::_construct();
    }

    public function getCanShowProductPrice($product)
    {
        return $this->getParentBlock()->getCanShowProductPrice($product);
    }

    public function getHidePrices()
    {
        return $this->getParentBlock()->getHidePrices();
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

        $parentBlock = $this->getParentBlock();

        if (is_null($product)) {
            $product = $this->getProduct();
        }

        return $parentBlock->_getPriceBlock($product->getTypeId())
                        ->setTemplate($parentBlock->getTierPriceTemplate())
                        ->setProduct($product)
                        ->setInGrouped($product->isGrouped())
                        ->setParent($parent)
                        ->callParentToHtml();
    }

    public function getAssociatedProducts()
    {
        return $this->getParentBlock()->getAssociatedProducts();
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

}
