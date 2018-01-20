<?php
 
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Link_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        $data = Mage::registry('navigation_link_data');
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('navigation_link_form', array('legend' => Mage::helper('adminhtml')->__('Navigation Link')));
        
        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'link_id',
        ));
        
        $fieldset->addField('display_title', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Display Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'display_title'
        ));
        
        $fieldset->addField('tooltip_title', 'text', array(
            'label' => Mage::helper('adminhtml')->__('ToolTip Title'),
            'name' => 'tooltip_title',
        ));
        
        $fieldset->addField('link_identifier', 'text', array(
            'label' => Mage::helper('adminhtml')->__('CSS Class Identifier'),
            'name' => 'link_identifier'
        ));
        
        $fieldset->addField('link_url', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Custom Url'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'link_url',
            'note' => 'include http:// for external links',
        ));
        

        $form->setValues($data);
        return parent::_prepareForm();
    }
}