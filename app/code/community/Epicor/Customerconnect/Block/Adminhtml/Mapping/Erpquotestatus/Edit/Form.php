<?php

class Epicor_Customerconnect_Block_Adminhtml_Mapping_Erpquotestatus_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{

    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getErpquoteStatusMappingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getErpquoteStatusMappingData();
            Mage::getSingleton('adminhtml/session')->getErpquotestatusMappingData(null);
        } elseif (Mage::registry('erpquotestatus_mapping_data')) {
            $data = Mage::registry('erpquotestatus_mapping_data')->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form(
            array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'mapping_form',
            array(
            'legend' => Mage::helper('adminhtml')->__('Mapping Information')
            )
        );

        $fieldset->addField(
            'code', 'text',
            array(
            'label' => Mage::helper('adminhtml')->__('Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code',
            'note' => Mage::helper('adminhtml')->__('Erp Quote Status Code'),
            )
        );


        $fieldset->addField(
            'state', 'select',
            array(
            'name' => 'state',
            'label' => Mage::helper('sales')->__('Erp Quote Status'),
            'class' => 'required-entry',
            'values' => Mage::getModel('customerconnect/config_source_quotestatus')->toOptionArray(),
            'required' => true,
            )
        );

        $data = $this->includeStoreIdElement($data);

        $form->setValues($data);

        return parent::_prepareForm();
    }

}
