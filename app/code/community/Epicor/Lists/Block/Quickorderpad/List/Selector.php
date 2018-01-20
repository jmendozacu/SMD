<?php

class Epicor_Lists_Block_Quickorderpad_List_Selector extends Epicor_Supplierconnect_Block_Customer_Info
{

    protected $_erpAccounts;

    public function _construct()
    {
        parent::_construct();

        $this->setTitle($this->__('List Selector'));
    }
    
    public function isSessionList($list)
    {
        $sessionList = $this->getHelper()->getSessionList();

        if ($sessionList) {
            return $sessionList->getId() == $list->getId();
        }
        
        return false;
    }

    public function getLists()
    {
        $helper = $this->getHelper();
        
        $lists = $helper->getQuickOrderPadLists();
        
        return $lists;
    }
    
    /*
     * Returns List Frontend Helper
     * 
     * @return Epicor_Lists_Helper_Frontend
     */
    public function getHelper()
    {
        return Mage::helper('epicor_lists/frontend');
    }

    public function getActionUrl()
    {
        return $this->getUrl('quickorderpad/form/listselect');
    }

    public function getReturnUrl()
    {
        $url = $this->getUrl('quickorderpad/return/results');
        return Mage::helper('epicor_comm')->urlEncode($url);
    }

}
