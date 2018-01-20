<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Suis_Options extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{options}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('', $this->__('None'));
            $this->addOption('customerconnect/erp_mapping_invoicestatus', $this->__('Invoice Status'));
        }
        return parent::_toHtml();
    }

}
