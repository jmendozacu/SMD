<?php

/**
 * 
 * Access management group contacts list
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_Details_Contacts extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'access_management_groups_details_contacts';
        $this->_blockGroup = 'epicor_common';
        $this->_headerText = Mage::helper('epicor_common')->__('Contacts');
        parent::__construct();
        $this->_removeButton('add');
    }

}
