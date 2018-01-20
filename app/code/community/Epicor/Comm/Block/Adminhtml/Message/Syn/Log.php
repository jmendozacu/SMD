<?php

class Epicor_Comm_Block_Adminhtml_Message_Syn_Log
        extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_message_syn_log';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('SYN Log');

        parent::__construct();
        
        $this->removeButton('add');
    }

}
