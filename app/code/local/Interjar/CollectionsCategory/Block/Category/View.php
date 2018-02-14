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
            ->setPageSize(8)
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
}
