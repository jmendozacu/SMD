<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Surs_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('rfq_number', $this->__('rfqNumber'));
            $this->addOption('line', $this->__('line'));
            $this->addOption('due_date', $this->__('dueDate'));
            $this->addOption('respond_date', $this->__('respondDate'));
            $this->addOption('decision_date', $this->__('decisionDate'));
            $this->addOption('product_code', $this->__('productCode'));
            $this->addOption('cross_reference', $this->__('crossReference'));
            $this->addOption('description', $this->__('description'));
            $this->addOption('status', $this->__('status'));
            $this->addOption('response', $this->__('response'));
        }
        return parent::_toHtml();
    }

}
