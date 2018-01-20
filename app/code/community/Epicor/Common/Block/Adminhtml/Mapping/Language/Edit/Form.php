<?php
 
class Epicor_Common_Block_Adminhtml_Mapping_Language_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getLanguageMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getLanguageMappingData();
            Mage::getSingleton('adminhtml/session')->getLanguageMappingData(null);
        }
        elseif (Mage::registry('language_mapping_data'))
        {
            $data = Mage::registry('language_mapping_data')->getData();
        }
        else
        {
            $data = array();
        }
        
        if(isset($data['language_codes']) && !is_array($data['language_codes'])) {
            $data['language_codes'] = explode(', ',$data['language_codes']);
        }

        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('mapping_form', array(
             'legend' =>Mage::helper('adminhtml')->__('Mapping Information')
        ));
 
        $fieldset->addField('erp_code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('ERP Code'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'erp_code',
        ));
        
        $fieldset->addField('language_codes', 'multiselect', array(
             'label'     => Mage::helper('adminhtml')->__('Locale Languages'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'language_codes',
             'values'    => Mage::getModel('adminhtml/system_config_source_locale')->toOptionArray(),
             'note'     => Mage::helper('adminhtml')->__('Magento Code'),
        ));

        $data = $this->includeStoreIdElement($data);

        $form->setValues($data);

        return parent::_prepareForm();
       
    }

}