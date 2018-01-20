<?php

/**
 * Parts crossreference grid setup
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Crossreference extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_parts_details_crossreference';
        $this->_blockGroup = 'supplierconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Cross Reference Part Information');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }
    
}
