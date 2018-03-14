<?php

/**
 * Block class for Ship status 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Shipstatus extends Epicor_Common_Block_Adminhtml_Mapping_Default_Grid {

    public function __construct() {
        $this->_controller = 'adminhtml_mapping_shipstatus';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Ship Status Mapping');
        $this->_addButtonLabel = Mage::helper('customerconnect')->__('Add Ship Status Mapping');
        parent::__construct();
    }

}
