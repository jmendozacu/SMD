<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Message_Log extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_message_log';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Message Log');

        parent::__construct();
        $ajaxUrl=$this->getUrl('adminhtml/epicorcomm_message_ajax/networktest');
        $javascript=" new Ajax.Request('$ajaxUrl', {
            method:     'get',
            onSuccess: function(transport){
            
                switch(transport.responseText) {

                    case 'true':
                        alert('Connection Successful');
                        break;
                        
                    case 'disabled':
                        alert('Connection Test Message Disabled\\nPlease Enable the Test Message and try again');
                        break;
                        
                    default:
                        alert('Connection Failed\\n'+transport.responseText);
                        break;
                }
            }
        });";
        
        $this->_addButton('networktest', array(
            'label'     => 'Test Network Connection',
            'onclick'   => $javascript,
            'class'     => 'add',
        ));
        $this->removeButton('add');
    }

}


