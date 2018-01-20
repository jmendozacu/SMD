<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cccs_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('contract_title', $this->__('contractTitle'));
            $this->addOption('account_number', $this->__('accountNumber'));
            $this->addOption('delivery_addresses_delivery_address', $this->__('deliveryAddress'));
            
        }
        return parent::_toHtml();
    }

}
