<?php

/**
 * Products Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */

class Epicor_Lists_Block_Customer_Account_List_Products extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'customer_account_list_products';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_lists')->__('Products');
        parent::__construct();
        $this->_removeButton('add');
    }    
}