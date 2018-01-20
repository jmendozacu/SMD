<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {


        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_sales_returns';
        $this->_blockGroup = 'epicor_comm';
        $this->_mode = 'view';

        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');
    }

    public function getHeaderText() {
        $header = Mage::helper('adminhtml')->__('Return');

        return $header;
    }

}
