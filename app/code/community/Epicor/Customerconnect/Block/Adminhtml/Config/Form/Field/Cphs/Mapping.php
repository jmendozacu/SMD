<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cphs_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('product_code', $this->__('productCode'));
            $this->addOption('unit_of_measure_code', $this->__('unitOfMeasureCode'));
            $this->addOption('total_qty_ordered', $this->__('totalQtyOrdered'));
            $this->addOption('last_ordered_date', $this->__('lastOrderedDate'));
            $this->addOption('last_order_number', $this->__('lastOrderNumber'));
            $this->addOption('last_tracking_number', $this->__('lastTrackingNumber'));
            $this->addOption('last_ordered_status', $this->__('lastOrderedStatus'));
            $this->addOption('last_packing_slip', $this->__('lastPackingSlip'));
        }
        return parent::_toHtml();
    }

}
