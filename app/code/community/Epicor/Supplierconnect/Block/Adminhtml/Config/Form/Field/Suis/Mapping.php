<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Suis_Mapping extends Mage_Core_Block_Html_Select {

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
            $this->addOption('purchase_order_number', $this->__('purchaseOrderNumber'));
            $this->addOption('supplier_reference', $this->__('supplierReference'));
            $this->addOption('currency_code', $this->__('currencyCode'));
            $this->addOption('goods_total', $this->__('goodsTotal'));
            $this->addOption('tax_amount', $this->__('taxAmount'));
            $this->addOption('grand_total', $this->__('grandTotal'));
            $this->addOption('balance_due', $this->__('balanceDue'));
            $this->addOption('invoice_status', $this->__('invoiceStatus'));
        }
        return parent::_toHtml();
    }

}
