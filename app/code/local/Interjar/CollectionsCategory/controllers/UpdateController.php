<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 31/01/2018
 * Time: 11:21
 */
class Interjar_CollectionsCategory_UpdateController extends Mage_Core_Controller_Front_Action
{
    /**
     * UpdateController Products Action to return Subcategory Product HTML
     */
    public function productsAction()
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        $page = $this->getRequest()->getParam('page');
        $bunch = $this->getRequest()->getParam('bunch');
        if ($categoryId && $page && $bunch) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $products = $this->getProductsFromCategory($category, $bunch, $page);
            if ($products) {
                /** @var Interjar_CollectionsCategory_Block_Category_Subcategory_Product $productsBlock */
                $productsBlock = $this->getLayout()->createBlock('collectionscategory/category_subcategory_product');
                $productsBlock->setSubcategory($category);
                $productsBlock->setProductCollection($products);
                $html = $productsBlock->toHtml();
                $response = [];
                $response['html'] = $html;
                $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
                $this->getResponse()->setBody(json_encode($response));
            }
        }
    }

    /**
     * @param $category
     * @param string|int$page
     * @return bool|Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductsFromCategory($category, $pageSize, $page)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addCategoryFilter($category)
            ->addAttributeToSelect('*')
            ->addUrlRewrite($category->getId())
            ->setPageSize($pageSize)
            ->setCurPage($page);
        if ($productCollection->getLastPageNumber() >= $page) {
            if ($productCollection->getSize()) {
                return $productCollection;
            }
        }
        return false;
    }

    public function subcategoryAction()
    {
        // TODO - Write action to return Subcategory HTML
    }
}
