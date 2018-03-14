<?php
/**
 * AR Payments Payment
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Lines extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_arpayments_details_lines';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('Lines');
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}