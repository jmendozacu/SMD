<?php
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
          
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'translation_edit_form',
                                      'action' => $this->getUrl('*/*/save'),                             
                                      'method' => 'post',
                                      'enctype' => 'multipart/form-data'
                                   )
      );    
        $form->addField('id', 'hidden', array(
            'name' => 'id',
        ));   
       
//        $form->addField('main_language', 'hidden', array(
//            'name' => 'main_language',
//        ));  
        $form->addField('translated_phrases', 'hidden', array(
            'name' => 'translated_phrases',           
        ));
        
        $form->setValues(array( 'id' => $this->getRequest()->getParam('id')));
// 
      $form->setUseContainer(true);
      $this->setForm($form);
      
      return parent::_prepareForm();
  }
}

