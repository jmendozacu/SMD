<?php

/**
 * Customer Period balances list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Account_Balances_Aged_List_Grid extends Epicor_Customerconnect_Block_Customer_Account_Balances_Grid {

    public function __construct() {
        parent::__construct();

        $this->setId('customer_account_agedbalances_list');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('customerconnect');

        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        //      $this->setRowUrlValue('*/*/editContact');

        $this->setMessageType('cuad');
        $this->setDataSubset('contacts/contact');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $details = Mage::registry('customer_connect_account_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */

        if($details) {
            $balanceInfo = $this->processBalances($details->getVarienDataArrayFromPath('account/aged_balances/aged_balance'));

            $this->setCustomColumns($balanceInfo['columns']);
            $this->setCustomData($balanceInfo['balances']);
        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());
        }
    }
}