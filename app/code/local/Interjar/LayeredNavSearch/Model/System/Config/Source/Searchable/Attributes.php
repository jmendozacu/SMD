<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 12/12/2017
 * Time: 16:18
 */
class Interjar_LayeredNavSearch_Model_System_Config_Source_Searchable_Attributes
{
    /**
     * Return all options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes */
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection');
        $options = [];
         /** @var Mage_Catalog_Model_Product_Attribute $attribute */
        foreach ($attributes as $attribute) {
             if (strlen(trim($attribute->getFrontendLabel())) > 0) {
                 $options[] = [
                     'value' => $attribute->getAttributeCode(),
                     'label' => $attribute->getFrontendLabel()
                 ];
             }
         }
         return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = $this->toOptionArray();
        $returnOptions = [];
        if (count($options) > 0) {
            foreach ($options as $option) {
                $returnOptions[$option['value']] = $option['label'];
            }
        }
        return $returnOptions;
    }
}
