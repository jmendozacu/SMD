<?php

/**
 * Customer AR  Payments Aged List
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Aged extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_arpayments_aged';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Aged Payments');
    }

}