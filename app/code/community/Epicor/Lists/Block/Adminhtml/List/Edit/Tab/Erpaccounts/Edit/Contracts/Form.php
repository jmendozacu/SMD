<?php

/**
 * List Contracts ERP Accounts Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Erpaccounts_Edit_Contracts_Form
    extends Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Contracts_Form
{

    protected function _prepareForm()
    {
        $this->_account = Mage::registry('customer_erp_account');
        $this->_type = 'erpaccount';
        return parent::_prepareForm();
    }

}
