<?php

/**
 * Layer helper.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Helper_Category extends Mage_Core_Helper_Abstract
{

    /**
     * @param Varien_Data_Tree_Node $node
     * @return string
     */
    public function getNodeUrl(Varien_Data_Tree_Node $node) 
    {
        return Mage::getSingleton('core/url')->getDirectUrl($node->getRequestPath());
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Category_Tree
     */
    public function getTree(Mage_Catalog_Model_Category $category) 
    {
        /** @var $tree Mage_Catalog_Model_Resource_Category_Tree */
        $tree = Mage::getResourceModel('catalog/category_tree');

        // Retrieve parent id
        $pathIds = $category->getPathIds();
        $parentId = $pathIds[2];

        $root = Mage::getModel('catalog/category')->load($parentId);
        $node = new Varien_Data_Tree_Node($root->getData(), $root->getId(), $tree);
        $node->setProductCount2($this->getCustomProductCount($root));
        $node->setRequestPath($node->getUrlPath());
        $tree->addNode($node);
        $level = $category->getLevel() - 1;
        if ($this->getExpandTree()) {
            $level = $this->getLevel();
        }
        $tree->load($parentId, $level);

        $tree->addCollectionData($this->_getDefaultCollection());

        if (!$this->getShowEmptyCategories()) {
            foreach ($tree->getNodes() as $node) {
                $count = $this->getCustomProductCount($node);
                if (!$node->getProductCount2()) {
                    $node->setProductCount2($count);
                }
                if (!$count) {
                    $tree->removeNode($node);
                    $parent = $node->getParent();
                    if ($parent) {
                        $parent->setChildrenCount($parent->getChildrenCount() - 1);
                    }
                }
            }
        }

        return $tree;
    }

    public function getCustomProductCount($category) 
    {
        $category = Mage::getModel('catalog/category')->load($category->getId());
        if ($category->getProductCollection() == null) {
            return 0;
        }
        $collection = $category->getProductCollection()
                ->addAttributeToSelect('stock_status')
                ->setVisibility(array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG))
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        if (!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock")) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        }
        return $collection->count();
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function getExpandTree($store = null) 
    {
        return Mage::getStoreConfigFlag('layer/category_tree/expand_tree', $store);
    }

    /**
     * @param mixed $store
     * @return int
     */
    public function getLevel($store = null) 
    {
        return (int) Mage::getStoreConfig('layer/category_tree/recursion_level', $store);
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function getShowProductsCount($store = null) 
    {
        return Mage::getStoreConfigFlag('layer/category_tree/show_products_count', $store);
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function getOnlyIncludeInMenu($store = null) 
    {
        return Mage::getStoreConfigFlag('layer/category_tree/only_include_in_menu', $store);
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function getShowEmptyCategories($store = null) 
    {
        return Mage::getStoreConfigFlag('layer/category_tree/show_empty_categories', $store);
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory() 
    {
        return Mage::getSingleton('catalog/layer')->getCurrentCategory();
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection
     * @throws Mage_Core_Exception
     */
    protected function _getDefaultCollection() 
    {
        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->joinUrlRewrite()
                ->setLoadProductCount(true)
                ->addFieldToFilter('level', array('gt' => 1))
                ->addAttributeToFilter('is_active', true);

        if ($this->getOnlyIncludeInMenu()) {
            $collection->addAttributeToFilter('include_in_menu', true);
        }

        $attributes = Mage::getConfig()->getNode('frontend/category/collection/attributes');
        if ($attributes) {
            $attributes = $attributes->asArray();
            $attributes = array_keys($attributes);
        }
        $collection->addAttributeToSelect($attributes);

        if (Mage::helper('layer')->isSearch()) {
            $categoryIds = $this->_getLayerCategoryIds(Mage::getSingleton('catalogsearch/layer'));
            $collection->addIdFilter($categoryIds);
        }

        return $collection;
    }

    /**
     * @param Mage_Catalog_Model_Layer $layer
     * @return array
     */
    protected function _getLayerCategoryIds(Mage_Catalog_Model_Layer $layer) 
    {
        $productIds = $layer->getProductCollection()->getAllIds();
        $resource = Mage::getSingleton('core/resource');
        $adapter = $resource->getConnection('core_read');
        $select = $adapter->select()
                ->from(array('cat_index' => $resource->getTableName('catalog_category_product_index')), 'category_id')
                ->where('product_id IN (?)', $productIds)
                ->group('category_id');

        return $adapter->fetchCol($select);
    }

}
