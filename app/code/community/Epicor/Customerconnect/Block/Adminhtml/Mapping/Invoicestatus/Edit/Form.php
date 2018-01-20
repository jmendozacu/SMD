<?php
 
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Invoicestatus_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getInvoicestatusMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getInvoicestatusMappingData();
            Mage::getSingleton('adminhtml/session')->getInvoicestatusMappingData(null);
        }
        elseif (Mage::registry('invoicestatus_mapping_data'))
        {
            $data = Mage::registry('invoicestatus_mapping_data')->getData();
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
             'label'     => Mage::helper('adminhtml')->__('Code'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'code',
             'note'     => Mage::helper('adminhtml')->__('Invoice Status Code'),
        ));
 
 
        $fieldset->addField('status', 'text',
            array(
                'name'      => 'status',
                'label'     => Mage::helper('sales')->__('Invoice Status'),
                'class'     => 'required-entry',
                'values'    => 'status',
                'required'  => true,
            )
        );
//
//        $fieldset->addField('state', 'text',
//            array(
//                'name'      => 'state',
//                'label'     => Mage::helper('sales')->__('Invoice State'),
//                'class'     => 'required-entry',
//                'values'    => 'state',
//                'required'  => true,
//            )
//        );

        $data = $this->includeStoreIdElement($data);
        
        $form->setValues($data);

        return parent::_prepareForm();
       
    }
}
