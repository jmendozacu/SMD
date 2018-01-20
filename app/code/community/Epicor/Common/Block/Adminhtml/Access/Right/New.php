<?php
 
class Epicor_Common_Block_Adminhtml_Access_Right_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_access_right';
        $this->_blockGroup = 'epicor_common';
        $this->_mode = 'new';
 
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'view_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'view_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('view_form').action+'back/edit/');
            }
        ";
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('New Mapping');
    }
 
}