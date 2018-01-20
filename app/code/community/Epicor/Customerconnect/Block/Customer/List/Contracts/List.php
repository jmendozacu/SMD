<?php

/**
 * Customer Invoices list
 */
class Epicor_Customerconnect_Block_Customer_List_Contracts_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_list_contracts_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Contracts');
    }

}