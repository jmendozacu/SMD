<?php

/**
 * Order history block override
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Order_History extends Mage_Sales_Block_Order_History
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/history.phtml');

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            //For AR Payments - Don't Show AR Payment Orders in History Screen
            ->addFieldToFilter('ecc_arpayments_invoice',0)
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
    }      

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
