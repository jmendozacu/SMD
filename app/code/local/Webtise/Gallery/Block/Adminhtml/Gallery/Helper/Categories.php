<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 16/06/2016
 * Time: 11:07
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Helper_Categories extends Varien_Data_Form_Element_Abstract
{

    public function __construct($data){
        parent::__construct($data);
        $this->setType('categories');
    }

    public function getElementHtml()
    {
        $html = $this->getContentHtml();
        $this->setClass('input-categories');
        return $html;
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {

        $content = Mage::getSingleton('core/layout')
            ->createBlock('gallery/adminhtml_gallery_helper_categories_content');

        $content->setId($this->getHtmlId() . '_content')
            ->setElement($this);
        return $content->toHtml();
    }

    /**
     * Retrieve data object related with form
     *
     * @return Mage_Catalog_Model_Product || Mage_Catalog_Model_Category
     */
    public function getDataObject()
    {
        return $this->getForm()->getDataObject();
    }

    /**
     * Retrieve attribute field name
     *
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    public function getAttributeFieldName($attribute)
    {
        $name = $attribute->getAttributeCode();
        if ($suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        }
        return $name;
    }

    /**
     * Check readonly attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute|string $attribute
     * @return boolean
     */
    public function getAttributeReadonly($attribute)
    {
        if (is_object($attribute)) {
            $attribute = $attribute->getAttributeCode();
        }

        if ($this->getDataObject()->isLockedAttribute($attribute)) {
            return true;
        }

        return false;
    }

    /**
     * Default sore ID getter
     *
     * @return integer
     */
    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    protected function _getUrl(){
        return $this->getValue();
    }

    public function getName(){
        return $this->getData('name');
    }
}