<?php
class Epicor_Common_Block_Adminhtml_Quickstart extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'epicor_common';
        $this->_controller = 'adminhtml_quickstart';
        $this->_mode = 'edit';
        $this->setUseAjax(true);
//          $this->setFormAction(Mage::getUrl('*/*/new'));     
        $this->_removeButton('add');
        $this->_removeButton('back');
        $this->_removeButton('delete');
        $this->_addButton('refresh', array(
            'label'     => Mage::helper('epicor_common')->__('Reload Page'),
            'onclick'   => 'setLocation(window.location.href)',
        ));
//        $this->_removeButton('save');        
//    
//     
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Quick Start');
    }
}