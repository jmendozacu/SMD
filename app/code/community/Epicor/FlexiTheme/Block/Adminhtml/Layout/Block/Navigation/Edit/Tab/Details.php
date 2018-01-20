<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form 
{


    protected function _prepareForm() {
        
        $model = Mage::registry('layout_block_data');
        $data = unserialize($model->getBlockExtra());
        
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('adminhtml')->__('Slider block information')));
        
        
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));
        
        $fieldset->addField('identifier', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Identifier'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'identifier',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
