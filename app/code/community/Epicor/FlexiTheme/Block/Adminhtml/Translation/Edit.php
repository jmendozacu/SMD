<?php
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                  
        $this->_objectId = 'id';
        $this->_blockGroup = 'flexitheme';
        $this->_controller = 'adminhtml_translation';
        $this->_mode = 'edit';
        $this->setUseAjax(true);
             
        $this->_removeButton('add');
        $this->_removeButton('delete');
        $this->_removeButton('reset');  
        $this->_removeButton('save');
        
       
        $this->_addButton('Auto Translate', array(
            'id'        => 'flexitheme_autotranslate',
            'label'     => Mage::helper('adminhtml')->__('Auto Translate All Phrases'),
            'onclick'   =>  "autoTranslate(translation_edit_form,'{$this->getUrl('*/*/autoTranslate')}')",
            'class'     => 'translate',
        ), -100);
            
        $this->_addButton('submit', array(
            'label'     => Mage::helper('adminhtml')->__('Save'),
            'onclick'   =>  "formSubmit(translation_edit_form,'{$this->getUrl('*/*/save')}')",             
            'class'     => 'save',
        ), -100); 
     
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Edit Translation Language');
    }
}
