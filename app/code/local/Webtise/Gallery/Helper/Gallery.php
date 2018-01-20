<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 09:11
 */

class Webtise_Gallery_Helper_Gallery extends Mage_Core_Helper_Abstract
{

    public function getFileBaseDir(){
        return Mage::getBaseDir('media').DS.'webtise'.DS.'gallery'.DS.'file';
    }

    public function getFileBaseUrl(){
        return Mage::getBaseUrl('media').'webtise'.'/'.'gallery'.'/'.'file';
    }

    public function getAttributeSourceModelByInputType($inputType){
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    public function getAttributeInputTypes($inputType = null){
        $inputTypes = array(
            'multiselect'   => array(
                'backend_model'     => 'eav/entity_attribute_backend_array'
            ),
            'boolean'       => array(
                'source_model'      => 'eav/entity_attribute_source_boolean'
            ),
            'file'          => array(
                'backend_model'     => 'gallery/gallery_attribute_backend_file'
            ),
            'image'         => array(
                'backend_model'     => 'gallery/gallery_attribute_backend_image'
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    public function getAttributeBackendModelByInputType($inputType){
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    public function galleryAttribute($gallery, $attributeHtml, $attributeName){
        $attribute = Mage::getSingleton('eav/config')->getAttribute(Webtise_Gallery_Model_Gallery::ENTITY, $attributeName);
        if ($attribute && $attribute->getId() && !$attribute->getIsWysiwygEnabled()) {
            if ($attribute->getFrontendInput() == 'textarea') {
                $attributeHtml = nl2br($attributeHtml);
            }
        }
        if ($attribute->getIsWysiwygEnabled()) {
            $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
        }
        return $attributeHtml;
    }

    protected function _getTemplateProcessor(){
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('gallery')->getPageTemplateProcessor();
        }
        return $this->_templateProcessor;
    }

}