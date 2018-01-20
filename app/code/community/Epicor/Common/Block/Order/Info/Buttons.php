<?php

/**
 * Orders info buttons block override
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Order_Info_Buttons extends Mage_Sales_Block_Order_Info_Buttons
{

    public function getReorderUrl($order)
    {
        return $this->getUrl('epicor/sales_order/reorder', array('order_id' => $order->getId()));
    }

}
