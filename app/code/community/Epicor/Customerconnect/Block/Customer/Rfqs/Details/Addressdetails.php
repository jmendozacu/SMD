<?php

/**
 * RFQ address details display 
 * 
 * Loaded by ajax to update addresses when address changed in dropdown
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Addressdetails extends Epicor_Customerconnect_Block_Customer_Address
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/rfqs/details/address_details.phtml');
    }
    
    public function showName() {
        return false;
    }

}
