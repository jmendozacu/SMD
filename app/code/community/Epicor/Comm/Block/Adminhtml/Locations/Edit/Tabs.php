<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Location');
    }

    protected function _beforeToHtml()
    {
        $location = Mage::registry('location');
        /* @var $location Epicor_Comm_Model_Location */

        if ($location->getId()) {
            $this->addTab('form_stores', array(
                'label' => 'Stores',
                'title' => 'Stores',
                'url' => $this->getUrl('*/*/stores', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax',
            ));


            $this->addTab('form_erp_accounts', array(
                'label' => 'ERP Accounts',
                'title' => 'ERP Accounts',
                'url' => $this->getUrl('*/*/erpaccounts', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax'
            ));

            $this->addTab('customers', array(
                'label' => 'Customers',
                'title' => 'Customers',
                'url' => $this->getUrl('*/*/customers', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax'
            ));

            $this->addTab('products', array(
                'label' => 'Products',
                'title' => 'Products',
                'url' => $this->getUrl('*/*/products', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax'
            ));
            
            $this->addTab('relatedlocations', array(
                'label' => 'Related Locations',
                'title' => 'Related Locations',
                'url' => $this->getUrl('*/*/relatedLocations', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax'
            ));
            $this->addTab('groupings', array(
                'label' => 'Groups',
                'title' => 'Groups',
                'url' => $this->getUrl('*/*/groupings', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax'
            ));
            
            $this->addTab('form_log', array(
                'label' => 'Message Log',
                'title' => 'Message Log',
                'url' => $this->getUrl('*/*/loggrid', array('id' => $location->getId(), '_current' => true)),
                'class' => 'ajax'
            ));
        }

        return parent::_beforeToHtml();
    }

}
