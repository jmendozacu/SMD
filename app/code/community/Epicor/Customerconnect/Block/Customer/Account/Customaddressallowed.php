<?php

/**
 * Customer Address Allowed
 */
class Epicor_Customerconnect_Block_Customer_Account_Customaddressallowed extends Epicor_Customerconnect_Block_Customer_Info {

     public function __construct() {
        parent::__construct();
            $this->setTemplate('customerconnect/customer/account/customaddressallowed.phtml');
     }   
}