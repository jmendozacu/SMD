<?php
 
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $data = Mage::registry('layout_block_data');


        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('adminhtml')->__('Layout block information')));

        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'block_id',
        ));
        
        $fieldset->addField('block_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'block_name',
        ));
        
        $fieldset->addField('block_type', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Type'),
            'name' => 'block_type',
        ));
        
        $fieldset->addField('block_template', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Default Template'),
            'name' => 'block_template',
        ));
        
        $fieldset->addField('block_template_left', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Left Template'),
            'name' => 'block_template_left',
        ));
        
        $fieldset->addField('block_template_right', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Right Template'),
            'name' => 'block_template_right',
        ));
        
        $fieldset->addField('block_template_header', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Header Template'),
            'name' => 'block_template_header',
        ));
        
        $fieldset->addField('block_template_footer', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Footer Template'),
            'name' => 'block_template_footer',
        ));
        
        $fieldset->addField('block_xml_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Xml Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'block_xml_name',
        ));
        
        $fieldset->addField('block_xml', 'textarea', array(
            'label' => Mage::helper('adminhtml')->__('Block Inner Xml'),
            'name' => 'block_xml',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}