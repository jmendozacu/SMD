<?php

class Epicor_Lists_Block_Adminhtml_List_Csvupload extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_comm')->__('Lists CSV Upload');
        $this->_mode = 'csvupload';

        parent::__construct();

        //$this->_removeButton('back');
        $this->_updateButton('save', 'label', Mage::helper('epicor_comm')->__('Upload'));
    }

}
