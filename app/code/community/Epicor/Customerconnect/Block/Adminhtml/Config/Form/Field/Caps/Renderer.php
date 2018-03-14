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

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Caps_Renderer extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{renderer}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('', $this->__('Default'));
            $this->addOption('Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_DeliveryAddress', $this->__('Delivery Address'));
            $this->addOption('Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_InvoiceAmount', $this->__('Invoice Amount'));
            $this->addOption('Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_PaymentValue', $this->__('Payment Value'));
            $this->addOption('Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_InvoiceBalance', $this->__('Invoice Balance'));
            //$this->addOption('Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_OutstandingAmount', $this->__('Term Amount'));
        }
        return parent::_toHtml();
    }

}
