<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Aged extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $columnIndex = $this->getColumn()->getIndex();
        $balanceData = $row->getData('aged_balances_aged_balance');
        if(!empty($balanceData)) {
            $i=1;
            $currencyCode = $row->getData('currency_code');
            $currency_code = Mage::helper('customerconnect')->getCurrencyMapping($currencyCode, Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
            $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
            foreach ($balanceData as $balance) {
                $number[$i] = $currency_symbol.$balance->getBalance();
                $i++;
            }
            return $number[$columnIndex];
        } else {
            unset($row);
        }
       
    }

}
