<?php

class Epicor_Comm_Block_Adminhtml_Locations_Groups extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_locations_groups';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Groups');
        $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add New Group');

        parent::__construct();
    }

}
