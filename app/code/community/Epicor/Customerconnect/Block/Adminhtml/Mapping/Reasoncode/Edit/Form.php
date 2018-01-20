<?php


class Epicor_Customerconnect_Block_Adminhtml_Mapping_Reasoncode_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getReasoncodeMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getReasoncodeMappingData();
            Mage::getSingleton('adminhtml/session')->getReasoncodeMappingData(null);
        }
        elseif (Mage::registry('reasoncode_mapping_data'))
        {
            $data = Mage::registry('reasoncode_mapping_data')->getData();
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

        $fieldset->addField('code', 'text', array(
            'label'     => Mage::helper('customerconnect')->__('Reason Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'code',
            'note'     => Mage::helper('adminhtml')->__('Code'),
        ));

        $fieldset->addField('description', 'text',
            array(
                'name'      => 'description',
                'label'     => Mage::helper('adminhtml')->__('Reason Code Description'),
                'class'     => 'required-entry',
                'values'    => 'status',
                'required'  => true,
            )
        );

        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('adminhtml')->__('Reason Code Type'),
            'name'      => 'type',
            'values'    => Mage::getModel('customerconnect/erp_mapping_reasoncodetypes')->toOptionArray(),
        ));

        $data = $this->includeStoreIdElement($data);

        $form->setValues($data);

        return parent::_prepareForm();

    }
} 