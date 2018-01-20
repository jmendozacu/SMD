<?php

/**
 * List Contracts ERP Accounts Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Customer_Edit_Contracts_Form extends Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Contracts_Form
{

    protected function _prepareForm()
    {
        $this->_account = Mage::registry('current_customer');
        //     $this->_account->setData('contract_shipto_default', $this->_account->getData('ecc_contract_shipto_default'));
        $this->_type = 'customer';
        return parent::_prepareForm();
    }

}
