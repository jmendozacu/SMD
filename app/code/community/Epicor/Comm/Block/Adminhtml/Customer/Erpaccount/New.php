<?php

/**
 * New Customer ErpAccount
 *
 * @author Gareth.James
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_New extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_controller = 'adminhtml_customer_erpaccount';
        $this->_blockGroup = 'epicor_comm';
        $this->_mode = 'new';

        $this->_updateButton('save', 'label', Mage::helper('epicor_comm')->__('Create ERP Account'));         
    }

    public function getHeaderText() {
        return Mage::helper('epicor_comm')->__('New ERP Account');
    }

}
