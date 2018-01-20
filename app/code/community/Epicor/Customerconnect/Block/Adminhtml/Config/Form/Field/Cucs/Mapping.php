<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Cucs_Mapping extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function setColumnName($value) {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->addOption('call_number', $this->__('callNumber'));
            $this->addOption('call_type', $this->__('callType'));
            $this->addOption('requested_date', $this->__('requestedDate'));
            $this->addOption('scheduled_date', $this->__('scheduledDate'));
            $this->addOption('actual_date', $this->__('actualDate'));
            $this->addOption('call_duration', $this->__('callDuration'));
            $this->addOption('service_status', $this->__('serviceStatus'));
            $this->addOption('invoiced', $this->__('invoiced'));
            $this->addOption('call_void', $this->__('callVoid'));
        }
        return parent::_toHtml();
    }

}
