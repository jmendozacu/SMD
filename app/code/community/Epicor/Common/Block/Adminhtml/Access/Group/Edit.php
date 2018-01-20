<?php
 
class Epicor_Common_Block_Adminhtml_Access_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_access_group';
        $this->_blockGroup = 'epicor_common';
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
        if (Mage::registry('access_group_data') && Mage::registry('access_group_data')->getId())
        {
            $title=Mage::registry('access_group_data')->getEntityName();
         //   $title= Mage::app()->getLocale()->getCountryTranslation($title);
            return Mage::helper('adminhtml')->__('Edit Access Group "%s"', $this->htmlEscape($title));
        } else {
            return Mage::helper('adminhtml')->__('New Access Group');
        }
    }
 
}