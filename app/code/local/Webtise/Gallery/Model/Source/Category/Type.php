<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 21/06/2016
 * Time: 14:52
 */

class Webtise_Gallery_Model_Source_Category_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('gallery')->__('All Categories'),
                    'value' =>  'all'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Anchor Categories'),
                    'value' =>  'category_anchor'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Non-Anchor Categories'),
                    'value' =>  'category_non_anchor'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Specific Category(s)'),
                    'value' =>  'specific_category'
                )
            );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

}