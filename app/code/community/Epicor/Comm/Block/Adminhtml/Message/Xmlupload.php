<?php
class Epicor_Comm_Block_Adminhtml_Message_Xmlupload extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_message';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('XML Upload');
        $this->_mode = 'xmlupload';
            
        parent::__construct();
        $this->_removeButton('back');
        
        $this->_updateButton('save', 'label', Mage::helper('epicor_comm')->__('Upload'));
    }
}