<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 31/01/2018
 * Time: 11:15
 */
class Interjar_CollectionsCategory_Block_Category_Subcategory_Product extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Catalog_Model_Category $subCategory
     */
    private $subCategory;

    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection $productCollection
     */
    private $productCollection;

    /**
     * Constructor to set template if not already set
     */
    protected function _construct()
    {
        parent::_construct();
        if (!$this->getTemplate()) {
            $this->setTemplate("catalog/collections-category/subcategory/product.phtml");
        }
    }

    /**
     * Set the Blocks SubCategory
     *
     * @param $category
     */
    public function setSubcategory($category)
    {
        $this->setData('subcategory', $category);
        $this->subCategory = $category;
    }

    /**
     * Set the Blocks Product Collection
     *
     * @param $productCollection
     */
    public function setProductCollection($productCollection)
    {
        $this->setData('productCollection', $productCollection);
        $this->productCollection = $productCollection;
    }

    /**
     * Return Subcategory
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getSubcategory()
    {
        return $this->subCategory;
    }

    /**
     * Return Product Collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        return $this->productCollection;
    }
}
