<?php
class   Epicor_FlexiTheme_Block_Adminhtml_Usertranslation_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
        
{
 
  public function __construct()
  {
      parent::__construct();      
      $this->setId('usertranslation_tabs');
      $this->setDestElementId('usertranslation_edit_form'); // this should be same as the form id in the Form.php file
       
      $this->setTitle(Mage::helper('adminhtml')->__('User Translations'));
  }
 
  protected function _beforeToHtml()
  {
     
        $block = $this->getLayout()->createBlock("flexitheme/adminhtml_usertranslation_edit_tabs_usertranslations");
        $this->addTab('display', array(
            'label' => Mage::helper('flexitheme')->__('Display User Translations'),
            'title' => Mage::helper('flexitheme')->__('Display User Translations'),
           'content' => $block->toHtml(),
              ));
        
        $block2=$this->getLayout()->createBlock('flexitheme/adminhtml_usertranslation_edit_tabs_addusertranslations');
        $this->addTab('add', array(
            'label' => Mage::helper('flexitheme')->__('Add User Translations'),
            'title' => Mage::helper('flexitheme')->__('Add User Translations'),
            'content'   => $block2->toHtml(),               
        ));
      
      return parent::_beforeToHtml();
  }
}

