<?php
 
class Epicor_Comm_Block_Adminhtml_Mapping_Shippingmethods_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getShippingmethodsMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getShippingmethodsMappingData();
            Mage::getSingleton('adminhtml/session')->getShippingmethodsMappingData(null);
        }
        elseif (Mage::registry('shippingmethods_mapping_data'))
        {
            $data = Mage::registry('shippingmethods_mapping_data')->getData();
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
        $fieldset->addField('shipping_method_code', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Shipping Method'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'shipping_method',
             'values'     => Mage::getModel('epicor_comm/erp_mapping_shipping')->toOptionArray(),
             'note'       => Mage::helper('adminhtml')->__('Required Shipping Method'),
        ));
        $fieldset->addField('erp_code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Code Value'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'erp_code',
        ));
        $data = $this->includeStoreIdElement($data);
        $form->setValues($data);

        return parent::_prepareForm();
       
    }
}