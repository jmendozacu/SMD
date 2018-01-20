<?php

class Epicor_Customerconnect_Block_Customer_Skus_List extends Epicor_Common_Block_Generic_List { // Mage_Adminhtml_Block_Widget_Grid_Container {//

    protected function _setupGrid() {
        $this->_controller = 'customer_skus_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('epicor_comm')->__('SKUs');
    }

}
