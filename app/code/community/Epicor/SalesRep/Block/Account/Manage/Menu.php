<?php

class Epicor_SalesRep_Block_Account_Manage_Menu extends Mage_Core_Block_Template
{

    private $_menuItems = array(
        '' => 'Details',
        'pricingrules' => 'Pricing Rules',
        'hierarchy' => 'Hierarchy',
        'salesreps' => 'Sales Reps',
        'erpaccounts' => 'ERP Accounts'
    );

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor/salesrep/account/manage/menu.phtml');
    }

    public function getMenuItems()
    {

        return $this->_menuItems;
    }

    public function getLink($link)
    {
        if (!empty($link)) {
            $link = '/' . $link;
        }

        return Mage::getUrl('salesrep/account_manage' . $link);
    }

    public function isCurrentPage($link)
    {
        $isPage = false;

        $action = Mage::app()->getRequest()->getActionName();

        if ($action == $link || ($action == 'index' && $link == '')) {
            $isPage = true;
        }

        return $isPage;
    }

}
