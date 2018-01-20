<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 13/06/2016
 * Time: 16:46
 */

class Webtise_Gallery_Model_Gallery_Attribute_Frontend_Image extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{

    public function getUrl($object, $size=null)
    {
        $url = false;
        $image = $object->getData($this->getAttribute()->getAttributeCode());

        if( !is_null($size) && file_exists(Mage::getBaseDir('media').DS.'webtise'.DS.'gallery'.DS.$size.DS.$image) ) {
            # resized image is cached
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media').'webtise/gallery/' . $size . '/' . $image;
        } elseif( !is_null($size) ) {
            # resized image is not cached
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl().'webtise/gallery/image/size/' . $size . '/' . $image;
        } elseif ($image) {
            # using original image
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media').'webtise/gallery/'.$image;
        }
        return $url;
    }

}