<?php

/**
 * Error grid container
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Errors extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_advanced_errors';
        $this->_blockGroup = 'epicor_common';
        $this->_headerText = Mage::helper('epicor_common')->__('Error Reports');
        parent::__construct();
        $this->removeButton('add');
    }

}
