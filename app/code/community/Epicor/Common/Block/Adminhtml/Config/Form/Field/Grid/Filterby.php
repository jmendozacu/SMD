<?php

class Epicor_Common_Block_Adminhtml_Config_Form_Field_Grid_Filterby extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{filter_by}" style="width:50px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('erp', $this->__('ERP'));
            $this->addOption('linq', $this->__('ECC'));
            $this->addOption('none', $this->__('No Filter'));
        }
        return parent::_toHtml();
    }

}
