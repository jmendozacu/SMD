<?php

/**
 * Recent Order block override
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Order_Recent extends Mage_Sales_Block_Order_Recent
{

    /**
     * Get order reorder url
     *
     * @param   Epicor_Comm_Model_Order $order
     * @return  string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('epicor/sales_order/reorder', array('order_id' => $order->getId()));
    }

}
