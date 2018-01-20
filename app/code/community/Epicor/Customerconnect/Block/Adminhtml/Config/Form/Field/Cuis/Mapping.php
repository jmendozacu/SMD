<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cuis_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {

            $this->addOption('invoice_number', $this->__('invoiceNumber'));
            $this->addOption('invoice_date', $this->__('invoiceDate'));
            $this->addOption('due_date', $this->__('dueDate'));
            $this->addOption('our_order_number', $this->__('ourOrderNumber'));
            $this->addOption('customer_reference', $this->__('customerReference'));
            $this->addOption('currency_code', $this->__('currencyCode'));
            $this->addOption('original_value', $this->__('originalValue'));
            $this->addOption('payment_value', $this->__('paymentValue'));
            $this->addOption('outstanding_value', $this->__('outstandingValue'));
            $this->addOption('invoice_status', $this->__('invoiceStatus'));
            $this->addOption('contracts_contract_code', $this->__('contractCode'));
        }
        return parent::_toHtml();
    }

}
