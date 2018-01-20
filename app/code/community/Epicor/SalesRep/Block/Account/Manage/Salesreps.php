<?php

/**
 * Sales Rep Account ERP Account List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Salesreps extends Epicor_SalesRep_Block_Account_Manage_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor/salesrep/account/manage/salesreps.phtml');
    }

}
