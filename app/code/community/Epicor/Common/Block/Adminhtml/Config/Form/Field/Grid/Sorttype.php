<?php

class Epicor_Common_Block_Adminhtml_Config_Form_Field_Grid_Sorttype extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{sort_type}" style="width:50px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('text', $this->__('text'));
            $this->addOption('date', $this->__('date'));
            $this->addOption('number', $this->__('number'));
        }
        return parent::_toHtml();
    }

}
