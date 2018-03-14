<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_OutstandingAmount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        if($row->getSelectArpayments() !="Totals") {
            $settlementDiscount = $row->getSettlementDiscount();
            if($settlementDiscount) {
                $getValue = $settlementDiscount->getValue();
                $getDate = $settlementDiscount->getDate();
                $outStandingValue = $row->getOutstandingValue();
                $subtraction = bcsub($outStandingValue,$settlementDiscount,2);                
            } else {
                $subtraction = "0.000";
            }
            if((strtotime($getDate) > strtotime('now')) && ($getValue >0) ) {
               $html .='<input type="hidden" name="settlement_discount" value="'.$getValue.'" id="settlement_discount_' . $row->getId() . '" class="settlement_discount"/>';
               $html .='<input type="hidden" name="settlement_term_amount" value="'.$subtraction.'" id="settlement_term_amount_' . $row->getId() . '" class="settlement_term_amount"/>';
               $html .= '<span class="price">'.$currencySymbol.$subtraction.'</span>'; 
            } else {
              $subtraction ="0.000";
              $html  .='<input type="hidden" name="settlement_term_amount" value="'.$row->getOutstandingValue().'" id="settlement_term_amount_' . $row->getId() . '" class="settlement_term_amount"/>';
              $html  .='<input type="hidden" name="settlement_discount" value="'.$subtraction.'" id="settlement_discount_' . $row->getId() . '" class="settlement_discount"/>';
              $html  .= '<span class="price">'.$currencySymbol.$row->getOutstandingValue().'</span>';
            }
        }  else {
            //$html = '<span class="price"><strong>'.$currencySymbol.($row->getOutstandingValue() - $settlementDiscount).'</strong></span>';
        }
        return $html;
    }
    
    public function renderCss()
    {
        return 'arpay-noalign';
    }     

}