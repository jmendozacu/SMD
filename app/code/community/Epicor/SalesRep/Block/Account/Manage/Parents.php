<?php

/**
 * Sales Rep Account Hierarchy Parents List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Parents extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'account_manage_parents';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Parent Accounts');
    }

}
