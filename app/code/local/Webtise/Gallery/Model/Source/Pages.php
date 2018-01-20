<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 10:28
 */

class Webtise_Gallery_Model_Source_Pages extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        $cms_pages = Mage::getModel('cms/page')->getCollection()
                    ->setOrder('title', 'ASC');
        $pages = array();
        if($cms_pages->getData()) {
            foreach ($cms_pages as $page) {
                $pages[] = array(
                    'label' => $page->getTitle(),
                    'value' => $page->getIdentifier()
                );
            }
            $pages = array_unique($pages, SORT_REGULAR);
        }
        if (is_null($this->_options)) {
            $options = array(
                array(
                    'label' => Mage::helper('gallery')->__('Basket'),
                    'value' =>  'basket'
                ),
                array(
                    'label' => Mage::helper('gallery')->__('Custom'),
                    'value' =>  'custom'
                )
            );
            $this->_options = array_merge($pages, $options);
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

}