<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Sales Rep Account');
    }

    /**
     * 
     * @return Epicor_SalesRep_Model_Account
     */
    public function getSalesRepAccount()
    {
        if (!$this->_salesrep) {
            $this->_salesrep = Mage::registry('salesrep_account');
        }

        return $this->_salesrep;
    }

    protected function _beforeToHtml()
    {
        $salesRep = $this->getSalesRepAccount();


        $this->addTab('form_erp_accounts', array(
            'label' => 'ERP Accounts',
            'title' => 'ERP Accounts',
            'url' => $this->getUrl('*/*/erpaccounts', array('id' => $salesRep->getId(), '_current' => true)),
            'class' => 'ajax'
        ));

        $this->addTab('salesreps', array(
            'label' => 'Sales Reps',
            'title' => 'Sales Reps',
            'url' => $this->getUrl('*/*/salesreps', array('id' => $salesRep->getId(), '_current' => true)),
            'class' => 'ajax'
        ));
        
//        $this->addTab('pricing_rules', array(
//            'label' => 'Pricing Rules',
//            'title' => 'Pricing Rules',
//            'url' => $this->getUrl('*/*/pricingrules', array('id' => $salesRep->getId(), '_current' => true)),
//            'class' => 'ajax'
//        ));

        $this->addTab('hierarchy', array(
            'label' => 'Hierarchy',
            'title' => 'Hierarchy',
            'url' => $this->getUrl('*/*/hierarchy', array('id' => $salesRep->getId(), '_current' => true)),
            'class' => 'ajax'
        ));

        return parent::_beforeToHtml();
    }

}
