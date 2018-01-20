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
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_erpaccount_attribute';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Erp Accounts');

        $this->addButton(20, array('label' => 'Cancel', 'onclick' => "accountSelector.closepopup()"), 1);

        parent::__construct();
        $this->removeButton('add');
    }

}
