<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 16/06/2016
 * Time: 11:07
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Helper_Gallery extends Varien_Data_Form_Element_Abstract
{

    public function getElementHtml()
    {
        $html = $this->getContentHtml();
        return $html;
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {

        /* @var $content Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content */
        $content = Mage::getSingleton('core/layout')
            ->createBlock('gallery/adminhtml_gallery_helper_gallery_content');

        $content->setId($this->getHtmlId() . '_content')
            ->setElement($this);
        return $content->toHtml();
    }

    public function getLabel()
    {
        return '';
    }

    /**
     * Check default value usage fact
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return bool
     */
    public function usedDefault($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($attributeCode);

        if (!$this->getDataObject()->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } else if ($this->getValue() == $defaultValue &&
            $this->getDataObject()->getStoreId() != $this->_getDefaultStoreId()) {
            return false;
        }
        if ($defaultValue === false && !$attribute->getIsRequired() && $this->getValue()) {
            return false;
        }
        return $defaultValue === false;
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

    public function toHtml()
    {
        return '<tr><td class="value" colspan="3">' . $this->getElementHtml() . '</td></tr>';
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
}