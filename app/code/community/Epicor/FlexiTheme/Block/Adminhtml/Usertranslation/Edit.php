<?php
class Epicor_Flexitheme_Block_Adminhtml_Usertranslation_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                  
        $this->_objectId = 'id';
        $this->_blockGroup = 'flexitheme';
        $this->_controller = 'adminhtml_usertranslation';
        $this->_mode = 'edit';
        $this->setUseAjax(true);
             
        $this->_removeButton('add');
        $this->_removeButton('delete');
        $this->_removeButton('reset');  
        $this->_removeButton('save');
        $this->_removeButton('back');
        
        $this->_addButton('Site Text Editor', array(
            'id'        => 'flexitheme_site_text_editor',
            'label'     => Mage::helper('adminhtml')->__('Site Text Editor'),
            'onclick'   =>  "setLocation('{$this->getUrl('*/flexitheme_translation/index')}')",
            'class'     => 'translate',
        ), -100);
                    
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Edit Translation Language');
    }
}
