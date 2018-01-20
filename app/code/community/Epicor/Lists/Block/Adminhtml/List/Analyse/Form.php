<?php

class Epicor_Lists_Block_Adminhtml_List_Analyse_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/analyse'),
            'method' => 'post'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('layout_block_form', array(
            'legend' => $this->__('Analyse Lists')
        ));
        
        $fieldset->addType('customer', 'Epicor_Comm_Block_Adminhtml_Form_Element_Customer');
        $fieldset->addType('erpaccount', 'Epicor_Comm_Block_Adminhtml_Form_Element_Erpaccount');
        
        $fieldset->addField('store_id', 'select', array(
            'label' => $this->__('Store'),
            'values' => Mage::getModel('epicor_comm/config_source_sync_stores')->toOptionArray($this->__('No Store Selected')),
            'name' => 'store_id'
        ));
        
        $fieldset->addField('customer_type', 'select', array(
            'label' => $this->__('Customer Type'),
            'name' => 'customer_type',
            'values' => array(
                array(
                    'label' => $this->__('No Customer Type Selected'),
                    'value' => ''
                ),
                array(
                    'label' => $this->__('B2B'),
                    'value' => 'B'
                ),
                array(
                    'label' => $this->__('B2C'),
                    'value' => 'C'
                )
            ),
        ));
        
        $fieldset->addField('erpaccount_id', 'erpaccount', array(
            'label' => $this->__('Erp Account'),
            'name' => 'erpaccount_id',
        ));
        
        $fieldset->addField('customer_id', 'customer', array(
            'label' => $this->__('Customer'),
            'name' => 'customer_id',
        ));
        
        $fieldset->addField('sku', 'product', array(
            'label' => $this->__('Product'),
            'name' => 'sku',
        ));
        
        $form->addValues($this->getRequest()->getPost());

        return parent::_prepareForm();
    }

}
