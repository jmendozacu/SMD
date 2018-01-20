<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cuss_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {

            $this->addOption('shipment_date', $this->__('shipmentDate'));
            $this->addOption('order_number', $this->__('orderNumber'));
            $this->addOption('packing_slip', $this->__('packingSlip'));
            $this->addOption('customer_reference', $this->__('customerReference'));
            $this->addOption('delivery_method', $this->__('deliveryMethod'));
        }
        return parent::_toHtml();
    }

}
