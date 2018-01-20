<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Erp Account');
    }

    protected function _beforeToHtml() {
        $customer = Mage::registry('customer_erp_account');
//      $leftBlock=$this->getLayout()->createBlock('core/text')
////                ->setText('<h1>Left Block</h1>');
        // add grid to erp info details tab
        $detailsBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_erpinfo');
        $detailsBlock->
                setChild('currencygrid', $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_erpcurrencygrid'));

        $this->addTab('form_details', array(
            'label' => 'Details',
            'title' => 'Details',
            'content' => $detailsBlock->toHtml(),
        ));

        $addressBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_address');

        $erpAccount = Mage::registry('customer_erp_account');

        if ($erpAccount->isTypeCustomer()) {
            $this->addTab('form_address', array(
                'label' => 'Addresses',
                'title' => 'Addresses',
                'content' => $addressBlock->toHtml(),
            ));
        }

        $this->addTab('form_customers', array(
            'label' => 'Customers',
            'title' => 'Customers',
            'url' => $this->getUrl('*/*/customers', array('id' => $customer->getId(), '_current' => true)),
            'class' => 'ajax',
        ));

        $this->addTab('form_stores', array(
            'label' => 'Stores',
            'title' => 'Stores',
            'url' => $this->getUrl('*/*/stores', array('id' => $customer->getId(), '_current' => true)),
            'class' => 'ajax',
        ));

        if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_SalesRep') && !$erpAccount->isTypeSupplier()) {
            $this->addTab('form_salesreps', array(
                'label' => 'Sales Reps',
                'title' => 'Sales Reps',
                'url' => $this->getUrl('*/*/salesreps', array('id' => $customer->getId(), '_current' => true)),
                'class' => 'ajax',
            ));
        }

        if (!$erpAccount->isTypeSupplier()) {
            $this->addTab('form_locations', array(
                'label' => 'Locations',
                'title' => 'Locations',
                'url' => $this->getUrl('*/*/locations', array('id' => $customer->getId(), '_current' => true)),
                'class' => 'ajax',
            ));
        }

        if ($erpAccount->isTypeCustomer()) {
//            $skuBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_sku');
            $this->addTab('form_sku', array(
                'label' => 'Customer Sku',
                'title' => 'Customer Sku',
                'url' => $this->getUrl('*/*/skutab', array('id' => $customer->getId(), '_current' => true)),
//                'content' => $skuBlock->toHtml(),
                'class' => 'ajax'
            ));
        }

        $hierarchyBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_hierarchy');

        $this->addTab('heirarchy', array(
            'label' => 'Hierarchy',
            'title' => 'Hierarchy',
            'content' => $hierarchyBlock->toHtml(),
        ));

        //$logBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_log');

        //Hide the Tab Master Shoppers and Lists (If the Account Type is Supplier)
        if (!$erpAccount->isTypeSupplier()) {        
            $this->addTab('ecc_master_shopper', array(
                'label' => 'Master Shoppers',
                'title' => 'Master Shoppers',
                'url' => $this->getUrl('*/*/mastershopper', array('id' => $customer->getId(), '_current' => true)),
                //'content' => $logBlock->toHtml(),
                'class' => 'ajax'
            ));
            $this->addTab('lists', array(
                'label' => 'Lists',
                'title' => 'Lists',
                'url' => $this->getUrl('*/*/lists', array('id' => $customer->getId(), '_current' => true)),
                'class' => 'ajax'
            ));
        }
        
        $this->addTab('form_log', array(
            'label' => 'Message Log',
            'title' => 'Message Log',
            'url' => $this->getUrl('*/*/logsgrid', array('id' => $customer->getId(), '_current' => true)),
            //'content' => $logBlock->toHtml(),
            'class' => 'ajax'
        ));
        return parent::_beforeToHtml();
    }

}
