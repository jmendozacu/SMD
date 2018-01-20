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
class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Popup extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_salesrep_popup';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Sales Rep Accounts');

        $this->addButton(20, array('label' => 'Cancel', 'onclick' => "accountSelector.closepopup()"), 1);

        parent::__construct();
        $this->removeButton('add');
    }

}
