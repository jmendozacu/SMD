<?php
 
class Epicor_Comm_Block_Adminhtml_Mapping_Currency_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getCurrencyMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getCurrencyMappingData();
            Mage::getSingleton('adminhtml/session')->getCurrencyMappingData(null);
        }
        elseif (Mage::registry('currency_mapping_data'))
        {
            $data = Mage::registry('currency_mapping_data')->getData();
        }
        else
        {
            $data = array();
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
 
        $fieldset->addField('magento_id', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Name'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'magento_id',
             'values'    => Mage::getModel('adminhtml/system_config_source_currency')->toOptionArray(false),
             'note'     => Mage::helper('adminhtml')->__('Magento Code'),
        ));
 
 
        $fieldset->addField('erp_code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('ERP Code'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'erp_code',
        ));

        $data = $this->includeStoreIdElement($data);
 
        $form->setValues($data);

        return parent::_prepareForm();
       
    }

}