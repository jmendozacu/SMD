<?php

/**
 * Epicor_Common_Block_Adminhtml_Advanced_Cleardata
 * 
 * Form Container for post Data Form
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Postdata_Upload extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_advanced_postdata';
        $this->_blockGroup = 'epicor_common';
        $this->_headerText = Mage::helper('epicor_common')->__('Post Data');
        $this->_mode = 'upload';

        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('epicor_common')->__('Post'));
        $this->_removeButton('back');
        
        
    }

}
