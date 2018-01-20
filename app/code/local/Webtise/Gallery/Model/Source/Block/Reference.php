<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 09/06/2016
 * Time: 13:40
 */

class Webtise_Gallery_Model_Source_Block_Reference extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Return options for block reference Gallery attribute
     * @return array
     */
    public function getAllOptions() {
        if (is_null($this->_options)) {
            $main = array(
                array(
                    'label' => Mage::helper('gallery')->__('Content'),
                    'value' =>  'content'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Header'),
                    'value' =>  'header'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Left'),
                    'value' =>  'left'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Right'),
                    'value' =>  'right'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Footer'),
                    'value' =>  'footer'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('After Body Start'),
                    'value' =>  'after_body_start'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Before Body End'),
                    'value' =>  'before_body_end'
                )
            );
            if($custom = $this->getCustomReferences()) {
                $main = array_merge($main, $custom);
            }
            $this->_options = $main;
        }
        return $this->_options;
    }

    /**
     * Return options
     * @return array
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }

    /**
     * Create array to combine with all options
     * with any custom block references from system configuration
     * @return array
     */
    public function getCustomReferences()
    {
        $custom = array();
        $refs = Mage::getStoreConfig('gallery/block_refs/custom');
        if($refs){
            $refs = unserialize($refs);
            if(is_array($refs)) {
                foreach($refs as $ref) {
                    $custom[] = array(
                            'label' => $ref['label'],
                            'value' => $ref['xml_name']
                    );
                }
            }
        }
        return $custom;
    }

}