<?php

class Epicor_Comm_Block_Adminhtml_Config_Form_Field_Payment extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {
	protected $_paymentMethodRenderer;
	protected $_chargeTypeeRenderer;

    protected function _getPaymentMethodRenderer() {
        if (!$this->_paymentMethodRenderer) {
            $this->_paymentMethodRenderer = Mage::getSingleton('core/layout')->createBlock(
                'epicor_comm/adminhtml_form_field_payment', '',
                array('is_render_to_js_template' => false)
            );
            $this->_paymentMethodRenderer->setInputName('paymentmethod')
            				  ->setClass('rel-to-selected');
        }
        return $this->_paymentMethodRenderer;
    }

    protected function _getChargeTypeRenderer() {
        if (!$this->_chargeTypeRenderer) {
            $this->_chargeTypeRenderer = Mage::getSingleton('core/layout')->createBlock(
                'epicor_comm/adminhtml_form_field_chargetype', '',
                array('is_render_to_js_template' => true)
            );
            $this->_chargeTypeRenderer->setInputName('paymentmethod')
            					  ->setClass('rel-to-selected');
        }
        return $this->_chargeTypeRenderer;
    }

    public function __construct() {
        $this->addColumn('paymentmethod', array(
            'label' => Mage::helper('epicor_comm')->__('Payment Method'),
            'style' => 'width:150px',
            'renderer' => $this->_getPaymentMethodRenderer(),
        ));
        $this->addColumn('chargetype', array(
            'label' => Mage::helper('epicor_comm')->__('Type'),
            'style' => 'width:120px',
            'renderer' => $this->_getChargeTypeRenderer(),
        ));
        $this->addColumn('amount', array(
            'label' => Mage::helper('epicor_comm')->__('Value'),
            'style' => 'width:75px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add');
        parent::__construct();
    }
}