<?php

/**
 * Sales Rep Account ERP Account List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Hierarchy extends Epicor_SalesRep_Block_Account_Manage_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor/salesrep/account/manage/hierarchy.phtml');
    }
    
    public function canAddChildrenAccounts(){
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        return $helper->canAddChildrenAccounts();
    }

}
