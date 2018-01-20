<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/importPost'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'layout_import_form',
            array('legend' => Mage::helper('adminhtml')->__('Import Theme'))
        );

        $fieldset->addField(
            'layout_name', 
            'text',
            array(
                'label' => Mage::helper('adminhtml')->__('Layout Name'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'layout_name',
            )
        );
        
        $fieldset->addField(
            'layout_file', 
            'file',
            array(
                'label' => Mage::helper('adminhtml')->__('Layout File'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'layout_file',
            )
        );

        return parent::_prepareForm();
    }

}
