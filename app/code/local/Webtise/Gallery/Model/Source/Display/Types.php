<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 02/06/2016
 * Time: 09:02
 */

class Webtise_Gallery_Model_Source_Display_Types extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions() {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('gallery')->__('Carousel'),
                    'value' =>  'carousel'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Single Image'),
                    'value' =>  'single'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Single Image /w Text Overlay'),
                    'value' =>  'single_text_overlay'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Symmetric Grid'),
                    'value' =>  'grid_even'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Unsymmetrical Grid'),
                    'value' =>  'grid_uneven'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Carousel /w Text Overlay'),
                    'value' =>  'carousel_text_overlay'
                )
            );
        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
