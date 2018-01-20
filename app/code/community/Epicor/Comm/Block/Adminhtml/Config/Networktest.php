<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Networktest
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Config_Networktest extends Mage_Adminhtml_Block_System_Config_Form_Field{
    /*
     * Set template
     */

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('epicor_comm/config/networktest.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl() {
        return Mage::helper('adminhtml')->getUrl('adminhtml/epicorcomm_message_ajax/networktest');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml() {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
            'id' => 'network_test_button',
            'label' => $this->helper('adminhtml')->__('Test Connection'),
            'onclick' => 'javascript:check(); return false;'
                ));

        return $button->toHtml();
    }

}


