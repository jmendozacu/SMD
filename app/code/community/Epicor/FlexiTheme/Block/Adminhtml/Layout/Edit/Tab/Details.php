<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm() {
        
        if (Mage::getSingleton('adminhtml/session')->getThemeData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getThemeData();
            Mage::getSingleton('adminhtml/session')->getThemeData(null);
        }
        elseif (Mage::registry('layout_data'))
        {
            $data = Mage::registry('layout_data')->getData();
        }
        else
        {
            $data = array();
        }
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('layout_form', array('legend' => Mage::helper('adminhtml')->__('Layout information')));

        $fieldset->addField('layout_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Layout Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'layout_name',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
