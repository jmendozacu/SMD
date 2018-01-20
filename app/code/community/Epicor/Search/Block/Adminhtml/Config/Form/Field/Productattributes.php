<?php

class Epicor_Search_Block_Adminhtml_Config_Form_Field_Productattributes extends Mage_Core_Block_Html_Select
{

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setColumnName($value)
    {
        return $this->setExtraParams('rel="#{code}" style="width:80px"');
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->getItems();
            foreach ($attributes as $attribute) {
                if ($attribute->getUsedInSearchOrdering()) {
                    $this->addOption($attribute->getAttributecode(), str_replace("'", "\'", $attribute->getFrontendLabel()? : $attribute->getAttributecode()));
                }
            }
        }
        return parent::_toHtml();
    }

}
