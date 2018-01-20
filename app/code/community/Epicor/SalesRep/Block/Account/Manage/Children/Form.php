<?php

/**
 * Sales Rep Account 
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Children_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('sales_rep_form', array('legend' => $this->__('Add Child Account')));

        $fieldset->addField('sales_rep_id', 'text', array(
            'label' => $this->__('Sales Rep Account Number'),
            'required' => true,
            'name' => 'child_sales_rep_account_id',
        ));

        $fieldset->addField('name', 'text', array(
            'label' => $this->__('Name'),
            'required' => true,
            'name' => 'child_sales_rep_account_name'
        ));

        $fieldset->addField('addChildAccount', 'submit', array(
            'value' => $this->__('Add'),
            'name' => 'addChildAccount',
            'class' => 'form-button',
        ));

        $this->setForm($form);
    }
}
