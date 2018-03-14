<?php
/**
 * AR Payments Admin Screen sales order totals block
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */

class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_Totals extends Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Totals//Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
    }
}
