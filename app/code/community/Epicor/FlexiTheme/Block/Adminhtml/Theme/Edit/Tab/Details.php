<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {

        if (Mage::getSingleton('adminhtml/session')->getThemeData()) {
            $data = Mage::getSingleton('adminhtml/session')->getThemeData();
            Mage::getSingleton('adminhtml/session')->getThemeData(null);
        } elseif (Mage::registry('theme_data')) {
            $data = Mage::registry('theme_data')->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = Mage::helper('flexitheme')->createFieldSet($form, 'Theme information', 'theme_form');
        $fieldset->addField('theme_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Theme Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'theme_name',
        ));

        $fieldset->addField('name_validation_url', 'hidden', array(
            'label' => '',
            'name' => 'name_validation_url',
        ));
        
        $data['name_validation_url'] = Mage::getUrl('*/*/validatename');

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
