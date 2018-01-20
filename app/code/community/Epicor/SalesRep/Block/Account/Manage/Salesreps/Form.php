<?php

/**
 * Sales Rep Account 
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Salesreps_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('sales_rep_form', array('legend' => $this->__('Add Sales Rep')));

        $fieldset->addField('sales_rep_id', 'text', array(
            'label' => $this->__('Sales Rep ID'),
            'required' => false,
            'name' => 'sales_rep_id',
        ));

        $fieldset->addField('first_name', 'text', array(
            'label' => $this->__('First Name'),
            'required' => true,
            'name' => 'first_name',
        ));

        $fieldset->addField('last_name', 'text', array(
            'label' => $this->__('Last Name'),
            'required' => true,
            'name' => 'last_name',
        ));

        $fieldset->addField('email_address', 'text', array(
            'label' => $this->__('Email Address'),
            'required' => true,
            'name' => 'email_address',
            'class' => 'validate-email'
        ));

        $fieldset->addField('addSalesRep', 'submit', array(
            'value' => $this->__('Add'),
            'name' => 'addSalesRep',
            'class' => 'form-button',
        ));

        $this->setForm($form);
    }
}
