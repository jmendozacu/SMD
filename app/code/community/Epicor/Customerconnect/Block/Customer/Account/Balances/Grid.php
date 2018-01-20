<?php

/**
 * Customer Period balances list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Account_Balances_Grid extends Epicor_Common_Block_Generic_List_Grid {

    /**
     * Processes balances into an array of balance column data for rendering
     * 
     * @param type $balanceData
     * 
     * @return array
     */
    protected function processBalances($balanceData) {
        $balances = array(
            0 => new Varien_Object()
        );
        $columns = array();

        foreach ($balanceData as $balance) {
            $number = $balance->getData('_attributes')->getNumber();
            $balances[0]->setData($number, $balance->getBalance());

                       
            $columns[$number] = array(
                'header' => Mage::helper('epicor_comm')->__($balance->getDescription()),
                'align' => 'left',
                'type'  => 'currency',
                'currency_code' => Mage::helper('customerconnect')->getCurrencyMapping(Mage::registry('customer_connect_account_details')->getAccount()->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO),
                'index' => $number,
            );
        }

        ksort($columns);

        return array(
            'balances' => $balances,
            'columns' => $columns,
        );
    }

    public function getRowUrl($row) {
        return false;
    }

}