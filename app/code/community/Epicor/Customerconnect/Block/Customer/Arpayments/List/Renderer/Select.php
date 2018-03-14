<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $outStandingAmount = $row->getOutstandingValue();
        $disableClass ='';
        $disable='';
        if($outStandingAmount <= 0) {
            $disable = 'disabled=disabled';
            $disableClass ='disable_check_arpayment';
        }
        if($row->getSelectArpayments() !="Totals") {
            $html = '<input type="checkbox" '.$disable.' name="select_arpayments[]" value="' . $row->getId() . '" id="select_arpayments' . $row->getId() . '" class="select_arpayments '.$disableClass.'"/>';
        } else {
            $url = Mage::getUrl('checkout/onepage/saveArPayment');
            $url1 = Mage::getUrl('checkout/onepage/saveArOrder');
            $sucess = Mage::getUrl('checkout/onepage/successAr');
            $html = '<script>
                    var accordion = new Accordion("checkoutSteps", ".step-title", true);
                    accordion.openSection("opc-payment");
                    var payment = new Payment("co-payment-form", "'.$url.'");
                    var review = new Review("'.$url1.'", "'.$sucess.'");
                    var quoteBaseGrandTotal = "";
                    var checkQuoteBaseGrandTotal = quoteBaseGrandTotal;                    
                    var checkout = new Checkout(accordion,{
                        progress: "",
                        review:  "",
                        saveMethod:  "",
                        failure:  ""}
                    );  
                </script>';
        }
        return $html;
    }

}
