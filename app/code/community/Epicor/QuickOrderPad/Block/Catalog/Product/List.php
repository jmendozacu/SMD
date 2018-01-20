<?php

class Epicor_QuickOrderPad_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{

    protected $_processedInstock = false;
    
    /**
     * Catalog Product collection
     *
     * 
     */
    protected $_productCollection;     

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

        if ($this->getRequest()->getParam('wishlist_next')) {
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

    public function getSortBy()
    {
        $sort_by = Mage::app()->getRequest()->getParam('sort_by', Mage::getSingleton('catalog/session')->getQopSortBy()) ? : 'uom';
        Mage::getSingleton('catalog/session')->setQopSortBy($sort_by);

        return $sort_by;
    }

    public function getPrimarySort()
    {
        #return 'uom';
        if ($this->getHelper()->isLocationsEnabled() && $this->getSortBy() == 'location') {
            return 'location';
        } else {
            return 'uom';
        }
    }

    public function getSecondarySort()
    {
        #return 'location';
        if ($this->getPrimarySort() == 'uom') {
            if ($this->getHelper()->isLocationsEnabled()) {
                return 'location';
            } else {
                return null;
            }
        } else {
            return 'uom';
        }
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
     * 
     * @param Epicor_Comm_Model_Product $product
     * @return Array
     */
    public function getPrimaryItems($product)
    {
        $items = array();
        switch ($this->getPrimarySort()) {
            case 'location' :

                if ($product->getTypeId() == 'grouped') {
                    foreach ($this->getUOMProducts($product) as $uomProd) {
                        $items = array_merge($items, $uomProd->getCustomerLocations());
                    }
                } else {
                    $items = $product->getCustomerLocations();
                }
                break;

            case 'uom':
                if ($product->getTypeId() == 'grouped') {
                    $items = $this->getUOMProducts($product);
                }
                if (empty($items)) {
                    $items[] = $product;
                }
                break;
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
                if ($product->getTypeId() == 'grouped') {
                    $items = $this->getUOMProducts($product, $primaryProduct->getLocationCode());
                }
                break;
        }

        if (empty($items)) {
            $items[] = $primaryProduct;
        }
        return $items;
    }

    public function getPrimaryRowspan($product)
    {
        $primaryProducts = $this->getPrimaryItems($product);
        $primary_rowspan = count($primaryProducts);
        foreach ($primaryProducts as $primaryProduct) {
            $primary_rowspan += $this->getSecondaryRowspan($product, $primaryProduct) - 1;
        }

        return $primary_rowspan;
    }

    public function getSecondaryRowspan($product, $primaryProduct)
    {
        $secondaryProducts = $this->getSecondaryItems($product, $primaryProduct);
        return count($secondaryProducts);
    }

    public function setProductData($productA, $productB)
    {
        $product = null;
        /* @var $product Epicor_Comm_Model_Product */
        $location = null;
        /* @var $location Epicor_Comm_Model_Location_Product */
        if ($productB instanceof Epicor_Comm_Model_Location_Product) {
            $location = $productB;
        } elseif ($productA instanceof Epicor_Comm_Model_Location_Product) {
            $location = $productA;
        }
        if ($productB instanceof Epicor_Comm_Model_Product) {
            $product = $productB;
        } elseif ($productA instanceof Epicor_Comm_Model_Product) {
            $product = $productA;
        }
        if ($location && $product) {
            $product->setToLocationPrices($location->getLocationCode());
        }
        Mage::unregister('current_loop_product');
        Mage::register('current_loop_product', $product);
        Mage::unregister('current_location');
        Mage::register('current_location', $location);
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
                if (($locationCode && $item->getLocation($locationCode) === false)
                    || ($this->useLists() && !$this->isInCurrentList($item->getId()))) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * Get if the stock column can be shown on the quick order pad search results
     *
     * @return bool
     */
    public function showStockLevelDisplay()
    {
        return Mage::getStoreConfig('Epicor_Comm/stock_level/display') != '';
    }

    public function showProductImageDisplay()
    {
        return Mage::getStoreConfigFlag('quickorderpad/general/show_quickorderpad_images');
    }

    public function productHasOptions($product, $required = false)
    {
        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        return $helper->productHasCustomOptions($product, $required);
    }

    /**
     * Retrieve loaded product collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */    
    
    protected function _getProductCollection()
    {
        
        if (is_null($this->_productCollection)) {
            $helper = Mage::helper('epicor_comm/product');
            /* @var $helper Epicor_Comm_Helper_Product */

            $listHelper = Mage::helper('epicor_lists/frontend_product');
            /* @var $listHelper Epicor_Lists_Helper_Frontend_Product */

            $csv = $this->getRequest()->getParam('csv');

            if ($csv == 1 || $this->useLists()) {
                if ($csv == 1) {
                    $productsId = $helper->getConfigureListProductIds();
                } else {
                    $list = $listHelper->getSessionList();
                    /* @var $list Epicor_Lists_Model_List */
                    if ($list) {
                        $productsId = $listHelper->getProductIdsByList($list, true);
                        Mage::unregister('QOP_list_product_filter_applied');
                        Mage::register('QOP_list_product_filter_applied', true);
                    } else {
                        $productsId = array();
                    }
                }

                $collection = Mage::getResourceModel('catalog/product_collection')
                        ->setStoreId(Mage::app()->getStore()->getId());

                $collection
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents();

                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

                $collection->addFieldToFilter('entity_id', array('in' => $productsId));

                $this->_productCollection = $collection;
            }
        }

        return parent::_getProductCollection();
    }

    public function getLoadedProductCollection()
    {
        $csv = $this->getRequest()->getParam('csv');
        if ($csv == 1) {
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId(Mage::app()->getStore()->getId());
            
            $helper = Mage::helper('epicor_comm/product');
            /* @var $helper Epicor_Comm_Helper_Product */
            $configurations = $helper->getConfigureListProductIds();

            $collection
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addAttributeToSelect('sku')
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents();

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            $collection->addFieldToFilter('entity_id', $configurations);
            Mage::dispatchEvent('epicor_comm_products_required_configuration', array('configuration' => $collection));

            $this->_productCollection = $collection;
            return $this->_productCollection;
        } else {
            $locHelper = Mage::helper('epicor_comm/locations');
            /* @var $locHelper Epicor_Comm_Helper_Locations */
            // limit search to products in selected list 
            $listHelper = Mage::helper('epicor_lists/frontend_product');
            /* @var $listHelper Epicor_Lists_Helper_Frontend_Product */
            $list = $listHelper->getSessionList();
            if (
                $locHelper->isLocationsEnabled() && Mage::registry('search-instock') ||
                $listHelper->listsEnabled() && $list
            ) {
                $collection = parent::getLoadedProductCollection();
                if ($locHelper->isLocationsEnabled() && Mage::registry('search-instock')) {
                    foreach ($collection as $key => $product) {
                        if (!$product->isSaleable()) {
                            $collection->removeItemByKey($key);
                        }
                    }
                    $collcount = count($collection);
                    Mage::unregister('Epicor_Locations_Paging');
                    Mage::register('Epicor_Locations_Paging', $collcount);
                }

                if ($listHelper->listsEnabled() && $list) {
                    $listProducts = $listHelper->getProductIdsByList($list, true);
                    if (!Mage::registry('QOP_list_product_filter_applied') && !empty($listProducts)) {
                        $collection->addFieldToFilter('entity_id', array('in' => $listProducts));
                        Mage::register('QOP_list_product_filter_applied', true);
                    }
                }
                
                $this->_productCollection = $collection;
                return $this->_productCollection;
            } else {
                return parent::getLoadedProductCollection();
            }
        }
    }

    public function getProductCollection()
    {
        return $this->_productCollection;
    }
    
    public function getHeaderText()
    {
        $listHelper = Mage::helper('epicor_lists/frontend');
        /* @var $listHelper Epicor_Lists_Helper_Frontend */
        
        return 'TEXT';
        
        $headerText = $this->getData('header_text');
        if ($headerText && $headerText !== false) {
            return 'HELLO';
        }
        
        if ($list = $listHelper->getSessionList()) {
            return $list->getTitle();
        }
        
        return false;
    }
    
    public function useLists()
    {
        if ($this->getRequest()->getParam('csv') !== 1
            && $this->helper('epicor_lists/frontend')->listsEnabled()
            && !$this->helper('catalogsearch')->getQueryText()) {
            return true;
        }
        
        return false;
    }
    
    public function isInCurrentList($productId)
    {
        $listHelper = Mage::helper('epicor_lists/frontend_product');
        /* @var $listHelper Epicor_Lists_Helper_Frontend_Product */
        
        $list = $listHelper->getSessionList();
        /* @var $list Epicor_Lists_Model_List */
        $productIds = $list ? $listHelper->getProductIdsByList($list) : array();
        
        return in_array($productId, $productIds);
    }

}
