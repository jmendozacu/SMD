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
class Epicor_Supplierconnect_Block_Adminhtml_Customer_Supplieraccount_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_supplieraccount_attribute';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Supplier Accounts');

        $this->addButton(20, array('label' => 'Cancel', 'onclick' => "accountSelector.closepopup()"), 1);

        parent::__construct();
        $this->removeButton('add');
    }

}
