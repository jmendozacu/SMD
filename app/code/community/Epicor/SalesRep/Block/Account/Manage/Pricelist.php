<?php

/**
 * Setting button for adding new List 
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Salesrep_Block_Account_Manage_Pricelist extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {        
        $this->_controller = 'account_manage_pricelist';
        $this->_blockGroup = 'epicor_salesrep';
        $this->_headerText = Mage::helper('epicor_salesrep')->__('Price Lists');
        parent::__construct();
        $this->_removeButton('add');
    }

}