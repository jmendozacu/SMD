<?php

/**
 * Sales Rep Account Hierarchy Children List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Children extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'account_manage_children';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Child Accounts');
    }

    protected function _prepareLayout()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canAddChildrenAccounts()) {
        $this->addButton('add_button',array(
                                'label' => Mage::helper('adminhtml')->__('Add'),
                                'onclick' => "javascript:\$('child_account_add_form').show()",
                                'class' => 'task'
                            ));
        }
        
        return parent::_prepareLayout();
    }

}
