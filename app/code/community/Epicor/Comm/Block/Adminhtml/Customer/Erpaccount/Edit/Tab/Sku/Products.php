<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Erpaccount
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Sku_Products extends Mage_Adminhtml_Block_Widget_Grid_Container {
     public function __construct() {
        $this->_controller = 'adminhtml_customer_erpaccount_edit_tab_sku_products';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Products');
        
        $this->addButton(20, array('label'=>'Cancel','onclick'=>"productSelector.closepopup()"), 1);
    
        parent::__construct();
        $this->removeButton('add');
        
     }
}


