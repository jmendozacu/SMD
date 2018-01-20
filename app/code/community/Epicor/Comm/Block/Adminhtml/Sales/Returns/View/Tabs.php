<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Return');
    }

    protected function _beforeToHtml() {
        $return = Mage::registry('return');

        $detailsBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_returns_view_tab_details');
        
        $this->addTab('form_details', array(
            'label' => 'Details',
            'title' => 'Details',
            'content' => $detailsBlock->toHtml(),
        ));
        
        $statusBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_returns_view_tab_status');
        
        $this->addTab('form_status', array(
            'label' => 'Status',
            'title' => 'Status',
            'content' => $statusBlock->toHtml(),
        ));

        $logBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_returns_view_tab_log');

        $this->addTab('form_log', array(
            'label' => 'Message Log',
            'title' => 'Message Log',
            'url' => $this->getUrl('*/*/logsgrid', array('id' => $return->getId(), '_current' => true)),
            'content' => $logBlock->toHtml(),
            'class' => 'ajax'
        ));
        
        return parent::_beforeToHtml();
    }
}
