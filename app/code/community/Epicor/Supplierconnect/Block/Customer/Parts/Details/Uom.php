<?php

/**
 * Parts uom grid setup
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Uom extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_parts_details_uom';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('UOM');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }
    
}
