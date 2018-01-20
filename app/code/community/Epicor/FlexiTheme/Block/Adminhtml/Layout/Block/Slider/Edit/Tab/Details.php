<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Slider_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form 
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
        
        $fieldset->addField('transition', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Transition Effect'),
            'name' => 'transition',
            'values' => Mage::getModel('flexitheme/config_source_slidertransitions')->toOptionArray(),
        ));
        
        $fieldset->addField('transition_time', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Transition Time'),
            'name' => 'transition_time',
            'class' => 'required-entry',
            'required' => true,
            'comment' => '2 Sec = 2000',
        ));
        
        $fieldset->addField('display_time', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Display Time'),
            'name' => 'display_time',
            'class' => 'required-entry',
            'required' => true,
            'comment' => '2 Sec = 2000',
        ));
        
        $fieldset->addField('paging', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Show Controls'),
            'name' => 'paging',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        
        $fieldset->addField('random', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Show in Random Order'),
            'name' => 'random',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
