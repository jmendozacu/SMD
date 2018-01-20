<?php

/**
 * Parts price breaks grid setup
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Breaks extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_parts_details_breaks';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Quantity Price Breaks');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }
    
}
