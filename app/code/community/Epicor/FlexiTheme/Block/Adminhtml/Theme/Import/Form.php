<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/import'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('theme_import_form', array('legend' => Mage::helper('adminhtml')->__('Import Theme')));


        $fieldset->addField('theme_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Theme Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'theme_name',
        ));
        $fieldset->addField('theme_file', 'file', array(
            'label' => Mage::helper('adminhtml')->__('Theme File'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'theme_file',
        ));

        return parent::_prepareForm();
    }

}
