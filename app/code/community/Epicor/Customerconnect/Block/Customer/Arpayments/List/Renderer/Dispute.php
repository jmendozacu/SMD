<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Dispute extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $outStandingAmount = $row->getOutstandingValue();
        $disableClass ='';
        $disable='';
        $html='';
        if($outStandingAmount <= 0) {
            $disable = 'disabled=disabled';
            $disableClass ='disable_check_arpayment';
        }
        if($row->getSelectArpayments() !="Totals") {
            $html .= '<input type="checkbox" '.$disable.' name="dispute_invoice[]" value="' . $row->getId() . '" id="dispute_invoices_' . $row->getId() . '" class="dispute_invoices '.$disableClass.'"/>';
            $html .='<div>';
            $html .='<div class="expand-row"><span  class="plus-minus" style="font-size: 27px;" id="'.$row->getId().'">+</span></div>'
                    . '<textarea style="display:none" class="dispute_invoices_comments" id="dispute_invoices_comments_'.$row->getId().'"></textarea>'
                    . '</div>';
        } else {
           
        }
        return $html;
    }

}
