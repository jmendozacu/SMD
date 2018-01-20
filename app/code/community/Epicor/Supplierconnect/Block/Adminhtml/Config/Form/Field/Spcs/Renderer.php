<?php

class Epicor_Supplierconnect_Block_Adminhtml_Config_Form_Field_Spcs_Renderer extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{renderer}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('', $this->__('Default'));
            $this->addOption('Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_Orderstatus', $this->__('Order Status'));
            $this->addOption('Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_Confirmed', $this->__('Order Confirmed'));
            $this->addOption('Epicor_Supplierconnect_Block_Customer_Orders_New_Renderer_Linkpo', $this->__('New PO Link'));
            $this->addOption('Epicor_Supplierconnect_Block_Customer_Orders_New_Renderer_Reject', $this->__('Reject New PO'));
            $this->addOption('Epicor_Supplierconnect_Block_Customer_Orders_New_Renderer_Confirm', $this->__('Confirm New PO'));
            $this->addOption('Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_State', $this->__('Address State'));
        }
        return parent::_toHtml();
    }

}
