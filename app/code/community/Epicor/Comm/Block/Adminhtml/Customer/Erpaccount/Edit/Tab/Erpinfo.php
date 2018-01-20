<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Erpinfo extends Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Abstract {

   public function __construct() {
        parent::_construct();
        $this->_title='Details';
        $this->setTemplate('epicor_comm/customer/erpaccount/edit/erpinfo.phtml');
    } 
    
    public function getCustomerGroup()
    {
        $customerGroupId = $this->getErpCustomer()->getMagentoId();
        $model = Mage::getModel('customer/group')->load($customerGroupId);
        return $model;
    }
    
}