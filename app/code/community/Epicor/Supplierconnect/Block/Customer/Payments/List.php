<?php

/**
 * Customer Payments list
 */
class Epicor_Supplierconnect_Block_Customer_Payments_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_payments_list';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Payments');
    }

}