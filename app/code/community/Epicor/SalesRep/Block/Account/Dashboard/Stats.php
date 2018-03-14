<?php

class Epicor_SalesRep_Block_Account_Dashboard_Stats extends Mage_Core_Block_Template
{

    /**
     *  @var Varien_Object 
     */
    protected $_stats = array();

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('epicor/salesrep/account/dashboard/stats.phtml');
        $this->setTitle($this->__('Stats'));
        $this->setColumnCount(2);

        $salesRepAccount = Mage::registry('sales_rep_account');
        /* @var $salesRepAccount Epicor_SalesRep_Model_Account */
        
        $this->_stats[1] = array(
            $this->__('Your ERP Accounts:') => array('value' => count($salesRepAccount->getErpAccounts(true)), 'link' => $this->getUrl('*/account_manage/erpaccounts')),
            $this->__('Your Price Lists:') => array('value' => 0, 'link' => '')
        );

        if (count($salesRepAccount->getChildAccounts(true)) > 0) {
            $this->_stats[2] = array(
                $this->__('Total ERP Accounts Under You:') => array('value' => count(array_unique($salesRepAccount->getMasqueradeAccounts(true))), 'link' => $this->getUrl('*/account_manage/erpaccounts')),
                $this->__('Total Sales Rep Price Lists:') => array('value' => 0, 'link' => ''),
                $this->__('Sales Reps Reporting to You:') => array('value' => count($salesRepAccount->getChildAccounts(true)), 'link' => $this->getUrl('*/account_manage/hierarchy'))
            );
        }
    }

    /**
     * 
     * @return Epicor_Supplierconnect_Helper_Data
     */
    public function getHelper($type = null)
    {
        return Mage::helper('epicor_salesrep');
    }

}
