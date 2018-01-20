<?php

/**
 * Customer Orders list
 */
class Epicor_Customerconnect_Block_Customer_Dashboard_Orders extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {        
        $this->_controller = 'customer_dashboard_orders';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Recent Orders');
       
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }
    
}