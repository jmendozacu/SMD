<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:44
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl(){
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('gallery/gallery_image')->getImageBaseUrl().$this->getValue();
        }
        return $url;
    }
}