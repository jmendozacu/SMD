<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_PaymentAmount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {

        $html = '';
        $disableClass ='';
        $id = $row->getId();
        $disable = false;
        $settlementDiscount = $row->getSettlementDiscount();
        if($settlementDiscount) {
            $getValue = $settlementDiscount->getValue();
            $getDate = $settlementDiscount->getDate();
            $outStandingValue = $row->getOutstandingValue();
            if((strtotime($getDate) > strtotime('now')) && ($getValue >0) ) {
                $outStandingAmount = bcsub($outStandingValue,$settlementDiscount,2);                
            } else {
                $outStandingAmount = $row->getOutstandingValue();
            }
        } else {
            $outStandingAmount = $row->getOutstandingValue();
        }        
        if($outStandingAmount <= 0) {
            $disable = 'disabled=disabled';
            $disableClass ='disable_check_arpayment';
        }
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $checkCaapActive = Mage::getModel('customerconnect/arpayments')->checkCaapActive();
        $style="";
        if(!$checkCaapActive) {
            $style="display:none;";
        }        
        if($row->getSelectArpayments() !="Totals") {
            if (!empty($id)) {
                $html .= '<input type="hidden" name="aroutstanding_value[]" value="'.$outStandingAmount.'" id="aroutstanding_value_' . $id . '" class="aroutstanding_value" style="'.$style.'"/>';
                $html .= '<input style="width:108px;'.$style.'" onfocus="checkOnFocus(this)"  onblur="checkArRowTotal(this,event)" type="text" '.$disable.'  name="arpayment_amount[]" value="0" id="arpayment_amount' . $id . '" class="arpayment_amount '.$disableClass.'"/>';
            }   $html .='<p><span id="balance_ar">Balance:<span class="price">'.$currencySymbol.'<span class="balance_ar">'.$outStandingAmount.'</span></span></p>';
        } else {
            $html .='<div style=" margin-left: -79px; margin-top: 0px;'.$style.'">';
            $html .='<table class="data atpaymenttable" cellspacing="0">';
            //$html .='<tr class="disable_check_arpayment"><td class="artdtotal"><span class="txtamnt">Outstanding Amount : </span></td><td><span class="price">'.$row->getOutstandingValue().'</span></td></tr>';
            //$html .='<tr class="disable_check_arpayment"><td class="artdtotal"><span class="txtamnt">Invoice Amount : </span></td><td><span class="price">'.$row->getOriginalValue().'</span></td></tr>';
            //$html .='<tr class="disable_check_arpayment"><td class="artdtotal"><span class="txtamnt">Invoice Balance : </span></td><td><span class="price">'.$row->getPaymentValue().'</span></td></tr>';
            $html .='<tr class="disable_check_arpayment showbuttonar"><td class="artdtotal"><span class="txtamnt"><strong>Payment Amount :</span></strong></td><td id="paymentamount_arpays">'.$currencySymbol.'<span class="price paymentamount_arpay" id="paymentamount_arpay">0.00</span</span></td></tr>';
            $html .='<tr class="disable_check_arpayment showbuttonar"><td class="make_arpayment" colspan="2"><button id="makearpayment" title="Make Payment" type="button" class="scalable task" onclick="proceedToPreview()" style=""><span><span><span>Make Payment</span></span>
                                </span>
                            </button></td></tr>';
            $html .='</table>';
            $html .='</div>';
        }
        return $html;
       
    }

}