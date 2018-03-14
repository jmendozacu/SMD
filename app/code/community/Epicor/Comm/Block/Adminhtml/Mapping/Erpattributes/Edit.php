<?php
 /*
  * Form to allow editing of epicor_comm/erp_mapping_attributes table
  */
class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  /*
  * Set up buttons to allow save and 'save and continue' buttons on grid for epicor_comm/erp_mapping_attributes table
  */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_mapping_erpattributes';
        $this->_blockGroup = 'epicor_comm';
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
 
    /*
  * Set up header text on grid for epicor_comm/erp_mapping_attributes table
  */
    public function getHeaderText()
    {
        if (Mage::registry('erpattributes_mapping_data') && Mage::registry('erpattributes_mapping_data')->getId())
        {
            return Mage::helper('adminhtml')->__('Edit Attribute Mapping');
        }else{
            return Mage::helper('adminhtml')->__('New Attribute Mapping');
        }
    }
 
}