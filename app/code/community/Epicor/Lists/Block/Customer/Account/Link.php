<?php

/**
 * Customer list management conditional block
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Link extends Mage_Core_Block_Abstract
{
    
    public function addLinkToParentBlock()
    {
        $parent         = $this->getParentBlock();
        $customer       = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        $show = true;
        $eccAccountType = $customer->getEccErpAccountType();
        //Manage Lists should not be available on the Supplier's "My Account" Menu
        if($eccAccountType =="supplier") {
            $show = false;
        }
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
        //&& $contractHelper->contractsEnabled()
        if ($parent && $contractHelper->listsEnabled() && $show) {
            $parent->addLink('List Management', 'lists/list', 'Manage Lists');
            //            $parent->addLink(
            //                'My Contracts', 'lists/contract', 'My Contracts'
            //            );
        }
    }
    
}