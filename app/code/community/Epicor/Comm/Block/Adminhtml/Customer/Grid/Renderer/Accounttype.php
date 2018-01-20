<?php

/**
 * Customer Account Type Grid Renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Grid_Renderer_Accounttype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render country grid column
     *
     * @param   Epicor_Comm_Model_Location_Product $row
     * 
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_common/account_selector');
        /* @var $helper Epicor_Common_Helper_Account_Selector */

        $accountType = $row->getEccErpAccountType();
        $accountTypes = $helper->getAccountTypes();
        $accountTypeLabel = isset($accountTypes[$accountType]) && isset($accountTypes[$accountType]['label']) ? $accountTypes[$accountType]['label'] : '';
        
        return $accountTypeLabel;
    }

}
