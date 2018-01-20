<?php

/**
 * Customer Orders list
 */
class Epicor_Customerconnect_Block_Customer_Dashboard_Invoices extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_dashboard_invoices';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Recent Invoices');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }
}