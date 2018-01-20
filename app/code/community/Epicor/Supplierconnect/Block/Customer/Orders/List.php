<?php

/**
 * Customer Orders list
 */
class Epicor_Supplierconnect_Block_Customer_Orders_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_orders_list';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Orders');
    }

}