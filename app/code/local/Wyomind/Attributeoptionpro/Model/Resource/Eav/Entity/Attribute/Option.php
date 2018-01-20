<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
class Wyomind_Attributeoptionpro_Model_Resource_Eav_Entity_Attribute_Option
    extends Mage_Eav_Model_Resource_Entity_Attribute_Option
{
    public function getAttributeOptionImages()
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getTable('eav/attribute_option'), array('option_id', 'image'));

        return $this->getReadConnection()->fetchPairs($select);
    }

    public function getAttributeOptionAdditionalImages()
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getTable('eav/attribute_option'), array('option_id', 'additional_image'));

        return $this->getReadConnection()->fetchPairs($select);
    }
}