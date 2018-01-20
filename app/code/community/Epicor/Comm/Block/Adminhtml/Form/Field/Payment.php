<?php

class Epicor_Comm_Block_Adminhtml_Form_Field_Payment extends Mage_Core_Block_Html_Select {
    private $_paymentMethods;

    protected function _getPaymentMethods() {
        if (is_null($this->_paymentMethods)) {
            $this->_paymentMethods = Mage::helper('payment')->getPaymentMethodList(true, true, true);
        }
        return $this->_paymentMethods;
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{paymentmethod}" style="width:150px');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
                $this->addOption('ALL', $this->__('ALL Payment Methods'));
            foreach ($this->_getPaymentMethods() as $pm) {
                $this->addOption($pm['value'], $pm['label']);
            }
        }
        return parent::_toHtml();
    }
}


