<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cuos_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('order_number', $this->__('orderNumber'));
            $this->addOption('order_date', $this->__('orderDate'));
            $this->addOption('customer_reference', $this->__('customerReference'));
            $this->addOption('currency_code', $this->__('currencyCode'));
            $this->addOption('original_value', $this->__('originalValue'));
            $this->addOption('order_status', $this->__('orderStatus'));
            $this->addOption('order_address', $this->__('orderAddress'));
            $this->addOption('order_address_street', $this->__('orderAddress > street'));
            $this->addOption('order_address_customer_code', $this->__('orderAddress > customerCode'));
            $this->addOption('order_address_name', $this->__('orderAddress > name'));
            $this->addOption('order_address_address1', $this->__('orderAddress > address1'));
            $this->addOption('order_address_address2', $this->__('orderAddress > address2'));
            $this->addOption('order_address_address3', $this->__('orderAddress > address3'));
            $this->addOption('order_address_city', $this->__('orderAddress > city'));
            $this->addOption('order_address_country', $this->__('orderAddress > country'));
            $this->addOption('order_address_postcode', $this->__('orderAddress > postcode'));
            $this->addOption('order_address_telephone_number', $this->__('orderAddress > telephoneNumber'));
            $this->addOption('order_address_fax_number', $this->__('orderAddress > faxNumber'));
            $this->addOption('contracts_contract_code', $this->__('contract > contractCode'));
        }
        return parent::_toHtml();
    }

}
