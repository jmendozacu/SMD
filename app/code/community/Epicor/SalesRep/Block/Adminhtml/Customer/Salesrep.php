<?php

/**
 * Sales Rep Grid Block
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_salesrep';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Sales Rep Accounts');
        parent::__construct();

        $this->updateButton('add', 'label', 'Add Sales Rep Account');
    }

}
