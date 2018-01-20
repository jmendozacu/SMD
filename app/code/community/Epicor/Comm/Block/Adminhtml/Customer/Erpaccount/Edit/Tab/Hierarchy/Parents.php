<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Hierarchy_Parents extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customer_erpaccount_edit_tab_hierarchy_parents';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Parents');
        parent::__construct();
        
        $this->removeButton('add');
    }

}
