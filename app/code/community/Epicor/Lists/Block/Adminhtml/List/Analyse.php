<?php

class Epicor_Lists_Block_Adminhtml_List_Analyse extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_comm')->__('Analyse Lists');
        $this->_mode = 'analyse';

        parent::__construct();
        $this->_removeButton('back');
        
        $this->_updateButton('save', 'label', $this->__('Analyse'));

    }

}
