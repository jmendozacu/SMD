<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 30/01/2018
 * Time: 15:49
 */
class Interjar_CollectionsCategory_Block_Category_View extends Mage_Catalog_Block_Category_View
{
    /**
     * Return collection of Subcategories
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection|bool
     */
    public function getSubcategoryCollection()
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = $this->getCurrentCategory();
        if ($category->getId()) {
            /** @var Mage_Catalog_Model_Resource_Category_Collection $subcategories */
            $subcategories = $category->getChildrenCategories();
            $subcategories->addAttributeToSelect('*');
            if ($subcategories->getSize()) {
                return $subcategories;
            }
        }
        return false;
    }

    /**
     * Return Product Collection from Category
     *
     * @param $category
     * @return Mage_Catalog_Model_Resource_Product_Collection|bool
     */
    public function getProductsFromCategory($category)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addCategoryFilter($category)
            ->addAttributeToSelect('*')
            ->addUrlRewrite($category->getId())
            ->setPageSize(
                $this->getProductLimit()
            )
            ->setCurPage(1);
        if ($productCollection->getSize()) {
            return $productCollection;
        }
        return false;
    }

    /**
     * Return Block of Subcategory Products
     *
     * @param $category
     * @param $products
     * @return string
     */
    public function getSubcategoryProductBlock($category, $products)
    {
        /** @var Interjar_CollectionsCategory_Block_Category_Subcategory_Product $productsBlock */
        $productsBlock = $this->getLayout()->createBlock('collectionscategory/category_subcategory_product');
        $productsBlock->setSubcategory($category);
        $productsBlock->setProductCollection($products);
        return $productsBlock->toHtml();
    }

    /**
     * @return int
     */
    public function getProductLimit()
    {
        $productLimit = Mage::getStoreConfig(
            'collectionscategory/general/subcategory_product_limit'
        );
        return $productLimit ? (int)$productLimit : 10;
    }

    /**
     * @param $category
     * @return bool
     */
    public function isSubcategoryDisplayCarousel($category)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('collection_display')
            ->addIdFilter([$category->getId()]);
        $categoryItem = $categoryCollection->getFirstItem();
        $categoryCollectionDisplay = $categoryItem->getCollectionDisplay();
        if ($categoryCollectionDisplay) {
            return $categoryCollectionDisplay == Interjar_CollectionsCategory_Model_Entity_Attribute_Source_Collection_Display::VALUE_CAROUSEL;
        }
        return false;
    }
}
