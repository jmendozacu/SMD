<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 22/06/2016
 * Time: 11:28
 */

class Webtise_Gallery_Model_Source_Cms_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('gallery')->__('All CMS Pages'),
                    'value' =>  'all'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Specific CMS page(s)'),
                    'value' =>  'specific_page'
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