<?php

/**
 * Reorder Sidebar override
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Sales_Reorder_Sidebar extends Mage_Sales_Block_Reorder_Sidebar
{

    /**
     * Check item product availability for reorder
     *
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return boolean
     */
    public function isItemAvailableForReorder(Mage_Sales_Model_Order_Item $orderItem)
    {
        $helper = $this->helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        if(!$helper->isFunctionalityDisabledForCustomer('cart')) {
            if ($orderItem->getProduct()) {
                return $orderItem->getProduct()->getStockItem()->getIsInStock();
            }
        }
        
        return false;
    }

}
