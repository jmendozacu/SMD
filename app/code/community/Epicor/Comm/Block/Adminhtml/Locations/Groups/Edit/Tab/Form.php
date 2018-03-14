<?php

class Epicor_Comm_Block_Adminhtml_Locations_Groups_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $helper = Mage::helper('epicor_comm');
        $group = Mage::registry('group');
        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (empty($formData)) {
            $formData = $group->getData();
        }
        $fieldset = $form->addFieldset('form_form', array('legend'=> $helper->__('Item information')));
                 
        $fieldset->addField('group_name', 'text', array(
            'label' => $helper->__('Group Name'),
            'required' => false,
            'name' => 'group_name',
        ));
        
        $fieldset->addField('group_expandable', 'select', array(
            'label' => $helper->__('Group Expandable'),
            'required' => false,
            'name' => 'group_expandable',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        
        $fieldset->addField('show_aggregate_stock', 'select', array(
            'label' => $helper->__('Show Aggregate Stock'),
            'required' => false,
            'name' => 'show_aggregate_stock',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        
        $fieldset->addField('enabled', 'select', array(
            'label' => $helper->__('Enabled'),
            'required' => false,
            'name' => 'enabled',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        
        $fieldset->addField('order', 'text', array(
            'label' => $helper->__('Sort Order'),
            'required' => false,
            'name' => 'order',
            'class' => 'validate-number',
        ));
        
        $form->setValues($formData);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}