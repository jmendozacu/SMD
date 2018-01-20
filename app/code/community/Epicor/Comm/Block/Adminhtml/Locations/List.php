<?php

class Epicor_Comm_Block_Adminhtml_Locations_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_locations_list';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Locations');
        $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add New Location');

        parent::__construct();
    }

}
