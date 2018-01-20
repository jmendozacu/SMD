<?php
class Epicor_Flexitheme_Block_Adminhtml_Translation extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
                  
        $this->_objectId = 'id';
        $this->_blockGroup = 'flexitheme';
        $this->_controller = 'adminhtml_translation';
//        $this->setUseAjax(true);
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('add');
        $this->_removeButton('back');
       
        $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Report');

         $this->_addButton('Admin', array(
            'id'        => 'flexitheme_admin',
            'label'     => Mage::helper('adminhtml')->__('Admin'),
            'onclick'   =>  "setLocation('{$this->getUrl('*/flexitheme_usertranslation/index')}')",
            'class'     => 'translate',
        ), -100);
            
        $this->_addButton('Update Phrases List', array(
            'id'        => 'flexitheme_update_translation_phrases_list',
            'label'     => Mage::helper('adminhtml')->__('Update Phrases List'),
            'onclick'   =>  "updatePhrases('{$this->getUrl('*/*/updatePhrases')}')",
//            'onclick'   =>  "controllerRedirect('{$this->getUrl('*/*/updatePhrases')}')",
            'class'     => 'save',
        ), -100); 
        
        $this->_addButton('addlanguage', array(
            'label'     => Mage::helper('adminhtml')->__('Add Language'),
            'onclick'   => "controllerRedirect('{$this->getUrl('*/*/edit')}')",
//            'onclick'   => "updatePhrases('{$this->getUrl('*/*/edit')}')",
            'class'     => 'save',
        ), -100);
      
     
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Site Text Editor');
    }
}
