<?php

/**
 * 
 * ERP Account grid for erp account selector input
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Adminhtml_Customer_Supplieraccount_Attribute_Grid extends Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Attribute_Grid
{

    protected function addAccountTypeFilter(&$collection)
    {
        $collection->addFieldToFilter('account_type', 'Supplier');
    }
}
