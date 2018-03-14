<?php

/**
 * Customer assigned list
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */

class Epicor_Lists_Block_Customer_Account_List_Customers extends Epicor_Common_Block_Generic_List {

    
   protected function _setupGrid() {
        $this->_controller = 'customer_account_list_customers';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_lists')->__('Customers');

    }
  protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

   
}
