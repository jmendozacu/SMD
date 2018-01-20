<?php
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Tabs_Details extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
          
  {
    
      if(Mage::registry('translation_language_data')){
          $language = Mage::registry('translation_language_data')->getData();
      }else{
          $language = false;
      }
     if (!$language){ $language = Array('translation_language'=>'','language_code'=>'','action'=>'');}
      $form = new Varien_Data_Form(array(
                                      'id' => 'translation_edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
                                      'enctype' => 'multipart/form-data'
                                   )
      );
 
      $this->setForm($form);
      
      $helper = Mage::helper('flexitheme');
      $fieldset = $form->addFieldset('display', array(
        'legend' => $helper->__('Language Settings'),
        'class' => 'fieldset-wide',  
      ));
      $fieldset->addField('translation_language', 'text', array(
        'name' => 'language',
        'label' => $helper->__('Language'),
        'style'   => "width:200px !important",
        'class'     => 'required-entry',           
        'required'  => true  
        ));
      $fieldset->addField('language_code', 'select', array(
        'name' => 'languageCode',
        'label' => $helper->__('Code'),
        'class' => 'required-entry', 
        'style'   => "width:200px !important",  
        'values'  => Mage::app()->getLocale()->getOptionLocales(),  
        'required'  => true,
        'after_element_html'=> '</br>If new code entered, value must not be currently allocated.' 
        ));
       $fieldset->addField('active', 'select', array(
        'name' => 'active',
        'label' => $helper->__('Active'),
        'class'     => 'validate-select', 
        'style'   => "width:100px !important",   
        'values' => array('Yes' => 'Yes','No' => 'No'),
        'required'  => true   
        ));
       if(Mage::registry('translation_language_data')){
            $form->setValues($language);
       }       
      return parent::_prepareForm();
  }
}

