<?php
/**
 * Global module observer.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_System_Config_Source_Filter
{
    /**
     * @var array
     */
    protected $_attributes;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => 'category',
                'label' => Mage::helper('layer')->__('Category'),
            ),
        );

        $attributes = $this->getAttributes();

        foreach ($attributes as $code => $attribute) {
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (($attribute->getIsFilterable() || $attribute->getIsFilterableInSearch())
                /*&& $attribute->getFrontendInput() != 'price'*/)
            {
                $options[] = array(
                    'value' => $code,
                    'label' => $attribute->getStoreLabel(),
                );
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        if (null === $this->_attributes) {
            $this->_attributes = array();
            $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
            $entity = $entityType->getEntity();

            /* @var $productAttributeCollection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setEntityTypeFilter($entityType->getEntityTypeId())
                ->addVisibleFilter()
                ->addToIndexFilter(true);

            $attributes = $productAttributeCollection->getItems();
            foreach ($attributes as $attribute) {
                /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                $attribute->setEntity($entity);
                $this->_attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $this->_attributes;
    }
}