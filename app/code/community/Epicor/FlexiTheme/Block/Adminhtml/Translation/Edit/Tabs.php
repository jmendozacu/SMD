<?php
class   Epicor_FlexiTheme_Block_Adminhtml_Translation_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
        
{
 
  public function __construct()
  {
      parent::__construct();      
      $this->setId('translation_tabs');
      $this->setDestElementId('translation_edit_form'); // this should be same as the form id in the Form.php file
      $language = Mage::getSingleton('flexitheme/translation_language')->load($this->getRequest()->getParam('id'))->getTranslationLanguage();
       
      $this->setTitle(Mage::helper('adminhtml')->__($language));
  }
 
  protected function _beforeToHtml()
  {
     
        $block=$this->getLayout()->createBlock('flexitheme/adminhtml_translation_edit_tabs_details');
        $language = Mage::getSingleton('flexitheme/translation_language')->load($this->getRequest()->getParam('id'))->getTranslationLanguage();
        $this->addTab('form_details', array(
            'label' => Mage::helper('flexitheme')->__('Edit Language'),
            'title' => Mage::helper('flexitheme')->__('Edit Language'),
            'content'   => $block->toHtml(),               
        ));

        $block2 = $this->getLayout()->createBlock("flexitheme/adminhtml_translation_edit_tabs_phrases");
        $this->addTab('text_section', array(
            'label' => Mage::helper('flexitheme')->__('Display Phrases'),
            'title' => Mage::helper('flexitheme')->__('Display Phrases'),
           'content' => $block2->toHtml(),
        ));
      
      return parent::_beforeToHtml();
  }
}

