<?php

class Epicor_Common_Block_Adminhtml_Config_Form_Field_Grid_Condition extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{condition}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('EQ', $this->__('EQ'));
            $this->addOption('NEQ', $this->__('NEQ'));
            $this->addOption('LIKE', $this->__('LIKE'));
            $this->addOption('LT', $this->__('LT'));
            $this->addOption('GT', $this->__('GT'));
            $this->addOption('LTE', $this->__('LTE'));
            $this->addOption('GTE', $this->__('GTE'));
            $this->addOption('LT/GT', $this->__('LT > GT'));
            $this->addOption('LTE/GTE', $this->__('LTE > GTE'));
        }
        return parent::_toHtml();
    }

}
