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
class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Caps_Options extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{options}" style="width:80px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('', $this->__('None'));
        }
        return parent::_toHtml();
    }

}
