<?php

class Epicor_Comm_Block_Adminhtml_Config_Form_Field_Crrs_Mapping extends Mage_Core_Block_Html_Select
{

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setColumnName($value)
    {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('erp_returns_number', $this->__('erpReturnsNumber'));
            $this->addOption('web_returns_number', $this->__('webReturnsNumber'));
            $this->addOption('rma_date', $this->__('rmaDate'));
            $this->addOption('returns_status', $this->__('returnsStatus'));
            $this->addOption('customer_reference', $this->__('customerReference'));
            $this->addOption('customer_code', $this->__('customerCode'));
            $this->addOption('customer_name', $this->__('customerName'));
            $this->addOption('invoice_number', $this->__('invoiceNumber'));
            $this->addOption('rma_case_number', $this->__('rmaCaseNumber'));
            $this->addOption('rma_contact', $this->__('rmaContact'));
        }
        return parent::_toHtml();
    }

}
