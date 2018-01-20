<?php

class Epicor_Common_Block_Adminhtml_Config_Form_Field_Grid_Type extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{type}" style="width:50px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('text', $this->__('text'));
            $this->addOption('date', $this->__('date'));
            $this->addOption('datetime', $this->__('datetime'));
            $this->addOption('number', $this->__('number'));
            $this->addOption('range', $this->__('range'));
            $this->addOption('options', $this->__('options'));
        }
        return parent::_toHtml();
    }

}
