<?php

class Epicor_Search_Block_Adminhtml_Config_Form_Field_Sortdirection extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{sort_dir}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('asc', $this->__('Ascending'));
            $this->addOption('desc', $this->__('Descending'));
        }
        return parent::_toHtml();
    }

}
