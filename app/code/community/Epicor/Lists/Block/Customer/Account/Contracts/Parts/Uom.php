<?php

/**
 * Customer Orders list
 */
class Epicor_Lists_Block_Customer_Account_Contracts_Parts_Uom extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_account_contracts_parts_Uom';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_lists')->__('Uom');
    }
}