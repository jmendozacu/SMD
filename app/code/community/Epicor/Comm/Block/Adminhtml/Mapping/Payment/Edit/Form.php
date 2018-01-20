<?php
 
class Epicor_Comm_Block_Adminhtml_Mapping_Payment_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getPaymentMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getPaymentMappingData();
            Mage::getSingleton('adminhtml/session')->getPaymentMappingData(null);
        }
        elseif (Mage::registry('payment_mapping_data'))
        {
            $data = Mage::registry('payment_mapping_data')->getData();
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
 
        $fieldset->addField('erp_code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Code'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'erp_code',
             'note'      => Mage::helper('adminhtml')->__('Order Status Code'),
        ));
        
        
 
 
        $fieldset->addField('magento_code', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Payment Method'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'magento_code',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_paymentmethods')->toOptionArray(),
            'note'       => Mage::helper('adminhtml')->__('Order Status Description'),
        ));
        
         $fieldset->addField('payment_collected', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Payment Collected'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'payment_collected',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_payment')->toOptionArray(),
             'note'      => Mage::helper('adminhtml')->__('Is payment collected by this payment method'),
        ));
         
         $fieldset->addField('gor_trigger', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Order Trigger'),
             'required'  => true,
             'name'      => 'gor_trigger',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_gortriggers')->toOptionArray(),
             'note'      => Mage::helper('adminhtml')->__('GOR only sent if condition is true'),
        ));
         
         $fieldset->addField('gor_online_prevent_repricing', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('GOR Online'),
             'required'  => false,
             'name'      => 'gor_online_prevent_repricing',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_yesnonull')->toOptionArray(),
             'note'      => Mage::helper('adminhtml')->__('Prevent Repricing for GOR for this Payment Method when order placed online?'),
        ));
         $fieldset->addField('gor_offline_prevent_repricing', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('GOR Offline'),
             'required'  => false,
             'name'      => 'gor_offline_prevent_repricing',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_yesnonull')->toOptionArray(),
             'note'      => Mage::helper('adminhtml')->__('Prevent Repricing for GOR for this Payment Method when order placed offline?'),
        ));
         
         $fieldset->addField('bsv_online_prevent_repricing', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('BSV Online'),
             'required'  => false,
             'name'      => 'bsv_online_prevent_repricing',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_yesnonull')->toOptionArray(),
             'note'      => Mage::helper('adminhtml')->__('Prevent Repricing for BSV for this Payment Method when order placed online?'),
        ));
         $fieldset->addField('bsv_offline_prevent_repricing', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('BSV Offline'),
             'required'  => false,
             'name'      => 'bsv_offline_prevent_repricing',
             'values'    => Mage::getModel('epicor_comm/erp_mapping_yesnonull')->toOptionArray(),
             'note'      => Mage::helper('adminhtml')->__('Prevent Repricing for BSV for this Payment Method when order placed offline?'),
        ));

        $data = $this->includeStoreIdElement($data);
 
        $form->setValues($data);

        return parent::_prepareForm();
       
    }
}