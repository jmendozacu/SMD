<?php

/**
 * Sales Rep Account Sales Rep List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Salesreps_List extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'account_manage_salesreps_list';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Sales Reps');
    }

}
