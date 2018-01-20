<?php

/**
 * Locations view block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Catalog_Product_View_Type_Grouped_Locations extends Mage_Catalog_Block_Product_View_Type_Grouped
{

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Get Locations Helper
     * @return Epicor_Comm_Helper_Locations
     */
    public function getHelper()
    {
        return $this->helper('epicor_comm/locations');
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
        $product->setToLocationPrices($location->getLocationCode());

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

        $product->setToLocationPrices($location->getLocationCode());

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

    public function getPrimarySort()
    {
        return Mage::getStoreConfig('epicor_comm_locations/frontend/product_details_sort');
    }

    public function getSecondarySort()
    {
        return $this->getPrimarySort() == 'uom' ? 'location' : 'uom';
    }

    /**
     * 
     * @param Epicor_Comm_Model_Product $product
     * @return Array
     */
    public function getPrimaryItems($product)
    {
        $items = array();
        switch ($this->getPrimarySort()) {
            case 'location' :
                foreach ($this->getUOMProducts($product) as $uomProd) {
                    /* @var $uomProd Epicor_Comm_Model_Product */
                    foreach ($uomProd->getCustomerLocations() as $locationCode => $location) {
                        if (!isset($items[$locationCode])) {
                            $items[$locationCode] = $location;
                        }
                    }
                }
                break;
            case 'uom':
                $items = $this->getUOMProducts($product);
                break;
        }

        if (empty($items)) {
            $items[] = $product;
        } else {
            $items = $this->filterItems($items, $this->getPrimarySort());
        }

        return $items;
    }

    /**
     * 
     * @param Epicor_Comm_Model_Product $product
     * @param Epicor_Comm_Model_Product|Epicor_Comm_Model_Location_Product $primaryProduct
     * @return type
     */
    public function getSecondaryItems($product, $primaryProduct)
    {
        $items = array();
        switch ($this->getSecondarySort()) {
            case 'location' :
                $items = $primaryProduct->getCustomerLocations();
                break;

            case 'uom':
                $items = $this->getUOMProducts($product, $primaryProduct->getLocationCode());
                break;
        }

        if (empty($items)) {
            $items[] = $primaryProduct;
        } else {
            $items = $this->filterItems($items, $this->getSecondarySort());
        }
        return $items;
    }

    /**
     * Gets an array of UOM products from a UOM product
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return array
     */
    public function getUOMProducts($product, $locationCode = null)
    {
        $result = array();
        if ($product->getTypeId() == 'grouped') {
            $result = $product->getTypeInstance(true)
                    ->getAssociatedProducts($product);
            $storeId = $product->getStoreId();
            foreach ($result as $key => $item) {
                /* @var $item Epicor_Comm_Model_Product */
                $item->setStoreId($storeId);
                if ($locationCode) {
                    if ($item->getLocation($locationCode) === false) {
                        unset($result[$key]);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Works out if we need to skip the row
     * 
     * @param Epicor_Comm_Model_Product $childProduct
     * @param Epicor_Comm_Model_Location_Product $location
     * 
     * @return boolean
     */
    public function filterItems($items, $type)
    {
        $filtered = $items;
        $itemId = $this->getRequest()->getParam('itemid');

        if ($itemId) {
            $cart = Mage::getSingleton('checkout/cart');
            /* @var $cart Mage_Checkout_Model_Cart */

            $cartItem = $cart->getQuote()->getItemById($itemId);
            /* @var $cartItem Mage_Sales_Model_Quote_Item */
            
            if ($cartItem) {
                foreach ($items as $x => $item) {
                    if ($type == 'uom' && $cartItem->getProductId() != $item->getId()) {
                        unset($filtered[$x]);
                    }
                    if ($type == 'location' && $cartItem->getEccLocationCode() != $item->getLocationCode()) {
                        unset($filtered[$x]);
                    }
                        
                }
            }
        }

        return $filtered;
    }
    
    public function allChildrenLocationCodes($product)
    {
        $locationCodes = array();
        foreach ($this->getUOMProducts($product) as $uomProduct) {
            foreach ($uomProduct->getCustomerLocations() as $locationCode => $location) {
                if (!in_array($locationCode, $locationCodes)) {
                    $locationCodes[] = $locationCode;
                }
            }
        }
        
        return $locationCodes;
    }

}
