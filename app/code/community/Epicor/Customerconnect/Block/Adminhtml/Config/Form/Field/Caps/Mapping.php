<?php
/**
 * Request CAPS - Customer AP Invoice Search – CAPS
 * 
 * 
 * 
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Caps_Mapping extends Mage_Core_Block_Html_Select {

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
            $this->addOption('original_value', $this->__('originalValue'));
            $this->addOption('payment_value', $this->__('paymentValue'));
            //$this->addOption('settlement_discount', $this->__('settlementDiscount'));
            $this->addOption('outstanding_value', $this->__('outstandingValue'));
            $this->addOption('delivery_addresses_delivery_address', $this->__('deliveryAddress'));
        }
        return parent::_toHtml();
    }

}