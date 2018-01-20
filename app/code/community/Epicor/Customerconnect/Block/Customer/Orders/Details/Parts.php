<?php

class Epicor_Customerconnect_Block_Customer_Orders_Details_Parts extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_orders_details_parts';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Parts');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
