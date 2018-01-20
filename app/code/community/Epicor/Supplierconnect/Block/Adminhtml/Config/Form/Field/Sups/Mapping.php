<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Sups_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('payment_date', $this->__('paymentDate'));
            $this->addOption('payment_reference', $this->__('paymentReference'));
            $this->addOption('currency_code', $this->__('currencyCode'));
            $this->addOption('payment_amount', $this->__('paymentAmount'));
            $this->addOption('invoice_number', $this->__('invoiceNumber'));
            $this->addOption('invoice_payment_amount', $this->__('invoicePaymentAmount'));
        }
        return parent::_toHtml();
    }

}
