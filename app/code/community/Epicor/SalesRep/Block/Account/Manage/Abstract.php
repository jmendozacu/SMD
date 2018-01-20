<?php

/**
 * Sales Rep Account Management Abstract block
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Abstract extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
    }

    public function canEdit()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        
        return $helper->canEdit();
    }

}
