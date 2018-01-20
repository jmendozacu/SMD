<?php

/**
 * Customer Shipments list
 */
class Epicor_Customerconnect_Block_Customer_Shipments_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_shipments_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Shipments');
    }

}