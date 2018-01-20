<?php

/**
 * Customer access groups grid 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_List extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'access_management_groups_list';
        $this->_blockGroup = 'epicor_common';
        $this->_headerText = Mage::helper('epicor_common')->__('Groups');

        $this->_addButton('module_controller', array(
            'label' => $this->__('Add Group'),
            'onclick' => "setLocation('{$this->getUrl('*/*/editgroup')}')",
            'class' => 'add'
        ));

        parent::__construct();
        $this->_removeButton('add');
    }

}
