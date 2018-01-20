<?php
 
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Servicecallstatus_Edit extends Epicor_Common_Block_Adminhtml_Mapping_Default_Edit
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_mapping_servicecallstatus';
        $this->_blockGroup = 'customerconnect';
        $this->_mode = 'edit';
 
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
 
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
 
    public function getHeaderText()
    {
        if (Mage::registry('servicecallstatus_mapping_data') && Mage::registry('servicecallstatus_mapping_data')->getCode())
        {
            $title=Mage::registry('servicecallstatus_mapping_data')->getCode();
            return Mage::helper('adminhtml')->__('Edit Mapping "%s"', $this->htmlEscape($title));
        } else {
            return Mage::helper('adminhtml')->__('New Mapping');
        }
    }
 
}