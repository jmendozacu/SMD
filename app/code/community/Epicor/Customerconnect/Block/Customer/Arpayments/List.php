<?php
/**
 * AR Payments
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_arpayments_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('AR Payments');
    }

}