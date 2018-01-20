<?php

class Epicor_Customerconnect_Block_Customer_Returns_Details_Statusinfo extends Epicor_Customerconnect_Block_Customer_Info
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/returns/status.phtml');

        $this->setTitle($this->__('Status Information :'));
        $this->setColumnCount(1);
    }

    public function getReturnMapping()
    {
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $helper = Mage::helper('customerconnect/messaging');
        /* @var $helper Epicor_Customerconnect_Helper_Messaging */

        return $helper->getRmaStatusMapping($return->getReturnsStatus());
    }

}
