<?php

class Epicor_Comm_Block_Adminhtml_Form_Field_EwaQuoteDisplayOptions extends Mage_Core_Block_Html_Select {
  
    protected function _getConfiguratorOptions() {       
        return Mage::helper('epicor_comm')->ewaConfiguratorValues();
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{ewaquotesortorder}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            foreach ($this->_getConfiguratorOptions() as $co) {                
                $this->addOption($co['value'], $co['label']);
            }
        }
        return parent::_toHtml();
    }
}


