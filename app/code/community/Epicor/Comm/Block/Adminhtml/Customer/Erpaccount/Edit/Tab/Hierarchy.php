<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Hierarchy extends Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Abstract
{

    public function __construct()
    {
        parent::_construct();
        $this->_title = 'Hierarchy';
        $this->setTemplate('epicor_comm/customer/erpaccount/edit/hierarchy.phtml');
    }

    public function getParentsHtml()
    {
        return Mage::app()->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_hierarchy_parents')->toHtml();
    }

    public function getChildrenHtml()
    {
        return Mage::app()->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_edit_tab_hierarchy_children')->toHtml();
    }

    public function getParentOptions()
    {
        $baseOptions = Epicor_Comm_Model_Erp_Customer_Group_Hierarchy::$linkTypes;
        
        $erpAccount = Mage::registry('customer_erp_account');
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        
        $parents = $erpAccount->getParents();
        
        $options = array();
        
        foreach ($baseOptions as $key => $val) {
            if(!isset($parents[$key])) {
                $options[$key] = $val;
            }
        }
        
        return $options;
    }
    
    public function getChildOptions()
    {
        return Epicor_Comm_Model_Erp_Customer_Group_Hierarchy::$linkTypes;
    }

}
