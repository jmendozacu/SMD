<?php

/**
 * Customer Service Call list
 */
class Epicor_Customerconnect_Block_Customer_Servicecalls_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_servicecalls_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Service Calls');
    }

}