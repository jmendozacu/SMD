<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View_Tab_Details_Attachments extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_sales_returns_view_tab_details_attachments';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Attachments');
        parent::__construct();

        $this->removeButton('add');
    }

}

