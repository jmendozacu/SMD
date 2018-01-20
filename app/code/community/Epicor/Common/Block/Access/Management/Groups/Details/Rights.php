<?php

/**
 * 
 * Access group rights list
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_Details_Rights extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'access_management_groups_details_rights';
        $this->_blockGroup = 'epicor_common';
        $this->_headerText = Mage::helper('epicor_common')->__('Rights');
        parent::__construct();
        $this->_removeButton('add');
    }

}
