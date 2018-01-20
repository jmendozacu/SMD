<?php

class Epicor_Customerconnect_Block_Customer_Invoices_Details_Lines extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_invoices_details_lines';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Lines');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}