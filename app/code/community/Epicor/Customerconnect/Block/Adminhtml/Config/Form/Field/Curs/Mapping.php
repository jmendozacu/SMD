<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Curs_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {

            $this->addOption('returns_number', $this->__('returnsNumber'));
            $this->addOption('line', $this->__('line'));
            $this->addOption('rma_date', $this->__('rmaDate'));
            $this->addOption('product_code', $this->__('productCode'));
            $this->addOption('revision_level', $this->__('revisionLevel'));
            $this->addOption('quantities_ordered', $this->__('quantities > ordered'));
            $this->addOption('quantities_returned', $this->__('quantities > returned'));
            $this->addOption('returns_status', $this->__('returnsStatus'));
            $this->addOption('order_number', $this->__('orderNumber'));
            $this->addOption('order_line', $this->__('orderLine'));
        }
        return parent::_toHtml();
    }

}
