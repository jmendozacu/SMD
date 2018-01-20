<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Template_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm() {
        
        $data = Mage::registry('layout_template_data')->getData();
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('layout_form', array('legend' => Mage::helper('adminhtml')->__('Template information')));


        $fieldset->addField('template_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Template Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'template_name',
        ));
        
        $fieldset->addField('template_file', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Template File Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'template_file',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
