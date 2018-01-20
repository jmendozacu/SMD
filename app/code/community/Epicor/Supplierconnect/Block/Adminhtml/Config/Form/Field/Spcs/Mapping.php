<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Spcs_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('purchase_order_number', $this->__('purchaseOrderNumber'));
            $this->addOption('order_date', $this->__('orderDate'));
            $this->addOption('order_status', $this->__('orderStatus'));
            $this->addOption('order_confirmed', $this->__('orderConfirmed'));
            $this->addOption('delivery_address', $this->__('deliveryAddress'));
            $this->addOption('delivery_address_street', $this->__('deliveryAddress > street'));
            $this->addOption('delivery_address_address_code', $this->__('deliveryAddress > addressCode'));
            $this->addOption('delivery_address_name', $this->__('deliveryAddress > name'));
            $this->addOption('delivery_address_address1', $this->__('deliveryAddress > address1'));
            $this->addOption('delivery_address_address2', $this->__('deliveryAddress > address2'));
            $this->addOption('delivery_address_address3', $this->__('deliveryAddress > address3'));
            $this->addOption('delivery_address_city', $this->__('deliveryAddress > city'));
            $this->addOption('delivery_address_county', $this->__('deliveryAddress > county'));
            $this->addOption('delivery_address_country', $this->__('deliveryAddress > country'));
            $this->addOption('delivery_address_postcode', $this->__('deliveryAddress > postcode'));
            $this->addOption('delivery_address_telephone_number', $this->__('deliveryAddress > telephoneNumber'));
            $this->addOption('delivery_address_fax_number', $this->__('deliveryAddress > faxNumber'));
            $this->addOption('delivery_address_carriage_text', $this->__('deliveryAddress > carriageText'));
            $this->addOption('confirm_po_change', $this->__('Confirm PO Change'));
            $this->addOption('reject_po_change', $this->__('Reject PO Change'));
        }
        return parent::_toHtml();
    }

}
