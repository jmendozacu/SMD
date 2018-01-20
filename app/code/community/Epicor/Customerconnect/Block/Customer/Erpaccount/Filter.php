<?php

/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method void setOnRight(bool $bool)
 * @method bool getOnRight()
 */
class Epicor_Customerconnect_Block_Customer_Erpaccount_Filter extends Mage_Directory_Block_Data
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/erpaccount/filter.phtml');

        $erpAcctId = Mage::getSingleton('customer/session')->getCustomer()->getErpaccountId();
        $erpAcct = Mage::getModel('epicor_comm/customer_erpaccount')->load($erpAcctId);
        /* @var $erpAcct Epicor_Comm_Model_Customer_Erpaccount */
        $this->setErpAccountId($erpAcct);
        $childArray = array();
        foreach ($erpAcct->getChildAccounts() as $child) {
            if (!in_array($child->getAccountNumber(), $childArray)) {
                $childArray[] = $child->getAccountNumber();
            }
        }

        $this->setChildAccounts($childArray);
    }

}
