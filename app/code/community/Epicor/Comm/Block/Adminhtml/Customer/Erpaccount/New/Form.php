<?php

/**
 * Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_New_Form
 * 
 * Form for createing a new ERP Account
 * 
 * @author Gareth.James
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_New_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/create'),
            'method' => 'post'
        ));

        $form->setUseContainer(true);
        
        
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
