<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Erpaccount
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_erpaccount';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Erp Accounts');
        parent::__construct();

        $erp = Mage::getStoreConfig('Epicor_Comm/licensing/erp');

        if (empty($erp) || !Mage::getStoreConfigFlag('epicor_comm_enabled_messages/cnc_request/active')) {
            $this->removeButton('add');
        } else {
            $this->updateButton('add', 'label', 'Add ERP Account');
        }
        
    }

}
