<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_PaymentValue extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        if($row->getSelectArpayments() !="Totals") {
            $html = '<span class="price">'.$currencySymbol.$row->getPaymentValue().'</span>';
        }  else {
            $html = '<span class="price"><strong>'.$currencySymbol.$row->getPaymentValue().'</strong></span>';
        }        

        return $html;
    }
    
    public function renderCss()
    {
        return 'arpay-noalign';
    }     

}