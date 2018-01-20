<?php

/**
 * Customer Orders list
 */
class Epicor_Customerconnect_Block_Customer_Account_Balances_Aged_List extends Epicor_Common_Block_Generic_List
{

    public function __construct()
    {
        parent::__construct();

        $details = Mage::registry('customer_connect_account_details');
        if (!is_null($details)) {
            $balanceInfo = $details->getVarienDataArrayFromPath('account/aged_balances/aged_balance');

            if (count($balanceInfo) == 0) {
                $this->setTemplate(false);
            }
        }
    }

    protected function _setupGrid()
    {
        $this->_controller = 'customer_account_balances_aged_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Aged Balances');
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
