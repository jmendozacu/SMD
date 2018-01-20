<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cups_Renderer extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{renderer}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('', $this->__('Default'));
            $this->addOption('Epicor_Customerconnect_Block_List_Renderer_Currency', $this->__('Currency'));
            $this->addOption('Epicor_Customerconnect_Block_List_Renderer_Linkinvoice', $this->__('Invoice Link'));
            $this->addOption('Epicor_Customerconnect_Block_List_Renderer_Linkorder', $this->__('Order Link'));
        }
        return parent::_toHtml();
    }

}
