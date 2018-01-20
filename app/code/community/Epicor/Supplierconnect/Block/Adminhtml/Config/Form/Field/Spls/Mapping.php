<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Spls_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('product_code', $this->__('productCode'));
            $this->addOption('cross_reference', $this->__('crossReference'));
            $this->addOption('cross_reference_type', $this->__('crossReferenceType'));
            $this->addOption('operational_code', $this->__('operationalCode'));
            $this->addOption('effective_date', $this->__('effectiveDate'));
            $this->addOption('expiration_date', $this->__('expirationDate'));
            $this->addOption('unit_of_measure_code', $this->__('unitOfMeasureCode'));
            $this->addOption('currency_code', $this->__('currencyCode'));
            $this->addOption('price', $this->__('price'));
        }
        return parent::_toHtml();
    }

}
