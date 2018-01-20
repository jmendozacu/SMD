
<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 12/12/2017
 * Time: 15:58
 */
class Interjar_LayeredNavSearch_Block_Catalog_Product_List_Toolbar extends Wyomind_Layer_Block_Catalog_Product_List_Toolbar
{
    /**
     * Set collection to pager
     *
     * @param Varien_Data_Collection $collection
     * @return Interjar_LayeredNavSearch_Block_Catalog_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        $collection = $this->getModifiedCollection($collection);
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;
    }

    /**
     * Modify our product list collection based on any search terms
     *
     * @param $collection
     * @return mixed
     */
    public function getModifiedCollection($collection)
    {
        if ($searchTerm = $this->getRequest()->getPost('st')) {
            // Splits search into array of words incase somebody searches for multiple terms of different attribute
            // eg. if someone searches for Black Dress, Black might be the colour and dress within the name
            $terms = [$searchTerm];
            $separateTerms = array_map('trim', explode(' ', $searchTerm));
            $allTerms = array_merge($terms, $separateTerms);
            $searchableAttributes = Mage::helper('layerednavsearch')->getSearchableAttributes();
            $searchableAttributes = explode(',', $searchableAttributes);
            if ($searchableAttributes) {
                foreach ($allTerms as $term) {
                    foreach ($searchableAttributes as $attributeCode) {
                        $collection->addAttributeToFilter($attributeCode, array(
                            array('like' => '% ' . $term . ' %'), //spaces on each side
                            array('like' => '% ' . $term), //space before and ends with term
                            array('like' => $term . ' %') // starts with term and space after
                        ));
                    }
                }
            }
        }
        return $collection;
    }
}