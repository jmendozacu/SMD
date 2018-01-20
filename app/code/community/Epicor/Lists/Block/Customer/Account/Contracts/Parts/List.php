<?php

/**
 * Customer Orders list
 */
class Epicor_Lists_Block_Customer_Account_Contracts_Parts_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_account_contracts_parts_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_lists')->__('Parts');

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        $details = Mage::registry('epicor_lists_contracts_details');
    }
}