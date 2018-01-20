<?php

/**
 * Return erp account renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Sales_Returns_Renderer_Erpaccount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return */
        
        $erpAccountName = $row->getData('customer_account_name');
        
        if(empty($erpAccountName)) {
            $erpAccountName = $row->getErpAccount()->getName();
        }
        
        return $erpAccountName;
    }

}
