<?php

/**
 * RFQ line attachments column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Contracts_parts_List_Renderer_Currencies extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_lists/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $currencies = $row->getCurrencies();
        
        $html = '';
        if($currencies){  
            $currencySymbol = '';
            $availableCurrencyCodes = Mage::app()->getStore()->getAvailableCurrencyCodes();
            foreach($currencies->getasarrayCurrency() as $currency){
                $currencyCode = Mage::helper('epicor_comm/messaging')->getCurrencyMapping($currency->getCurrencyCode(), 'e2m');
                $availableCurrencyCode = in_array($currencyCode, $availableCurrencyCodes) ? $currencyCode : null;
                $currencySymbol = $availableCurrencyCode ? Mage::app()->getLocale()->currency($availableCurrencyCode)->getSymbol() : null;
                $html .= $currencySymbol." ".$currency->getContractPrice()."</br>";             
            }        
        }
        
        return $html;
    }

}
