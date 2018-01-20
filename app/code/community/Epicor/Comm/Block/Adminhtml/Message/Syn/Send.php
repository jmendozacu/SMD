<?php
/**
 * Epicor_Comm_Block_Adminhtml_Message_Syn_Send
 * 
 * Form Block for SYN Send form
 * 
 * @author Gareth.James
 */
class Epicor_Comm_Block_Adminhtml_Message_Syn_Send extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_message_syn';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Send SYN request');
        $this->_mode = 'send';
            
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
                        alert('Connection Failed');
                        break;
                }
            }
        });";
        
        $this->_addButton('networktest', array(
            'label'     => 'Test Network Connection',
            'onclick'   => $javascript,
            'class'     => 'add',
        ));
        
        $this->_removeButton('back');
        
        $this->_updateButton('save', 'label', Mage::helper('epicor_comm')->__('Send SYN'));
    }

}
