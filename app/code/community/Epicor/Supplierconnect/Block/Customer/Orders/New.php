<?php

/**
 * Customer Orders list
 */
class Epicor_Supplierconnect_Block_Customer_Orders_New extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_orders_new';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('New Purchase Orders');
    }

}