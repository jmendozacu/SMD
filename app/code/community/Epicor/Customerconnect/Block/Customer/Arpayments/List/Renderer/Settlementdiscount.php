<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Settlementdiscount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    
    public function render(Varien_Object $row)
    {
        $html ='<input type="hidden" name="settlement_discount" value="'.$this->_getValue($row).'" id="settlement_discount_' . $row->getId() . '" class="settlement_discount"/>';
        return $html;
    }    
}
