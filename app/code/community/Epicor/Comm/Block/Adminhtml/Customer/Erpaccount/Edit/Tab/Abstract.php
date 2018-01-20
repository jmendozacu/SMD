<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface  
{
     protected $_erp_customer;
     protected $_title = 'Title';

     /**
      * 
      * @return Epicor_Comm_Model_Customer_Erpaccount
      */
    public function getErpCustomer() {
        if (!$this->_erp_customer) {
            $this->_erp_customer = Mage::registry('customer_erp_account');
        }
        return $this->_erp_customer;
    }
    
   public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return $this->_title;
    }

    public function getTabTitle() {
         return $this->_title;
    }

    public function isHidden() {
        return false;
    }
}

