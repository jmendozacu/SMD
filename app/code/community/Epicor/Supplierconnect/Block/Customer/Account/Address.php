<?php

class Epicor_Supplierconnect_Block_Customer_Account_Address extends Epicor_Supplierconnect_Block_Customer_Address
{

    public function _construct()
    {
        parent::_construct();
        if ($details = Mage::registry('supplier_connect_account_details'))
            $this->_addressData = $details->getSupplierAddress();
        
        $this->setTitle($this->__('Supplier Information'));
    }

}