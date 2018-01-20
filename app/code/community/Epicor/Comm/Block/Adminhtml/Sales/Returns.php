<?php

/**
 * Description of Return
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Adminhtml_Sales_Returns extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_sales_returns';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Returns');
        parent::__construct();

        $this->removeButton('add');
    }

}
