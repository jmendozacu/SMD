<?php
 
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_layout_block';
        $this->_blockGroup = 'flexitheme';
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
        if (Mage::registry('layout_block_data') && Mage::registry('layout_block_data')->getId())
        {
            $title=Mage::registry('layout_block_data')->getBlockName();
            return Mage::helper('adminhtml')->__('Edit Block "%s"', $this->htmlEscape($title));
        } else {
            return Mage::helper('adminhtml')->__('New Block');
        }
    }
 
}