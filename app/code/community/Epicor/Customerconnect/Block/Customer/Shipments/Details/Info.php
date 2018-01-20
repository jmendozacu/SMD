<?php

class Epicor_Customerconnect_Block_Customer_Shipments_Details_Info extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_shipments_details_info';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Shipments');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}