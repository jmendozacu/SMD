<?php

class Epicor_Comm_Block_Adminhtml_Config_Form_Field_Erpaccount extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $newElement = new Epicor_Comm_Block_Adminhtml_Form_Element_Erpaccount($element->getData());
        
        $newElement->setForm($element->getForm());
        
        return $newElement->getElementHtml();
    }
}
