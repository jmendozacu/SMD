<?php
 
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Page_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $data = Mage::registry('layout_page_data');


        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('layout_page_form', array('legend' => Mage::helper('adminhtml')->__('Layout page information')));

        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'page_id',
        ));
        
        $fieldset->addField('page_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Page Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'page_name',
        ));
        
        $fieldset->addField('xml_node', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Page Xml Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'xml_node',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}