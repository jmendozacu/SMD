<?php

/**
 * 
 * Form for post Data
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Postdata_Upload_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/postdata'),
            'method' => 'post'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('epicor_common')->__('Post Data')));
  
        $fieldset->addField('post_data_store_id', 'select', array(
            'name' => 'post_data_store_id',
            'label' => $this->__('Store(s)'),
            'title' => $this->__('Store(s)'),
            'class'     => 'validate-select',
            'required' => true,
            'values' => Mage::helper('epicor_common')->getAllStoresFormatted(),
        ));
        

        $fieldset->addField(
                'xml', 'textarea', array(
            'label' => Mage::helper('epicor_common')->__('XML Message'),
            'class'     => 'required-entry',        
            'name' => 'xml'
                )
        );

        $form->setValues(Mage::registry('posted_xml_data'));
        return parent::_prepareForm();
    }   

}
