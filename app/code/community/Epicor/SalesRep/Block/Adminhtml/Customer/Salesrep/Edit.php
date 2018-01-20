<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edit
 *
 * @author David.Wylie
 */
class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customer_salesrep';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('epicor_salesrep')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        
        $this->_updateButton('save', 'label', Mage::helper('epicor_salesrep')->__('Save'));

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * 
     * @return Epicor_SalesRep_Model_Account
     */
    public function getSalesRepAccount()
    {
        if (!$this->_salesrep) {
            $this->_salesrep = Mage::registry('salesrep_account');
        }
        
        return $this->_salesrep;
    }

    public function getHeaderText()
    {
        $salesRepAccount = $this->getSalesRepAccount();
        $name = $salesRepAccount->getName();
        $code = $salesRepAccount->getSalesRepId();
        
        if($salesRepAccount->isObjectNew()) {
            $header = Mage::helper('epicor_salesrep')->__('New Sales Rep Account');
        } else {
            $header = Mage::helper('epicor_salesrep')->__('Sales Rep Account: ' . $name . ' (' . $code . ')');
        }
        
        return $header;
    }

}
