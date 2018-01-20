<?php
/**
 * Define which product attributes can be displayed on category level.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_Catalog_Category_Attribute_Source_Layer_Product_Attributes
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected function _getAttributes()
    {
        $setIds = Mage::getModel('catalog/product')->getCollection()->getSetIds();
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setAttributeSetFilter($setIds)
            ->addIsFilterableFilter()
            ->setOrder('frontend_label', 'ASC');

        return $collection;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $attributes = $this->_getAttributes();
            foreach ($attributes as $attribute) {
                $this->_options[] = array(
                    'label' => Mage::helper('catalog')->__($attribute['frontend_label']),
                    'value' => $attribute['attribute_code']
                );
            }
        }

        return $this->_options;
    }
}