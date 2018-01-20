<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
class Wyomind_Attributeoptionpro_Model_Eav_Entity_Attribute_Source_Table
    extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getOptionImage($value)
    {
        $options = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setPositionOrder('asc')
            ->setAttributeFilter($this->getAttribute()->getId())
            ->setStoreFilter($this->getAttribute()->getStoreId())
            ->load()
            ->toArray();
        foreach ($options['items'] as $item) {
            if ($item['option_id'] == $value) {
                return $item['image'];
            }
        }
        return false;
    }

    public function getOptionAdditionalImage($value)
    {
        $options = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setPositionOrder('asc')
            ->setAttributeFilter($this->getAttribute()->getId())
            ->setStoreFilter($this->getAttribute()->getStoreId())
            ->load()
            ->toArray();
        foreach ($options['items'] as $item) {
            if ($item['option_id'] == $value) {
                return $item['additional_image'];
            }
        }
        return false;
    }
}