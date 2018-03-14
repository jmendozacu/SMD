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
    
    
    public function __construct()
    {
        parent::__construct();

        //TODO: add full name logic
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute(
                'shipping_firstname',
                'order_address/firstname',
                'shipping_address_id',
                null,
                'left'
            )
            ->joinAttribute(
                'shipping_middlename',
                'order_address/middlename',
                'shipping_address_id',
                null,
                'left'
            )
            ->joinAttribute(
                'shipping_lastname',
                'order_address/lastname',
                'shipping_address_id',
                null,
                'left'
            )
            ->addAttributeToFilter(
                'customer_id',
                Mage::getSingleton('customer/session')->getCustomer()->getId()
            )
            ->addAttributeToFilter(
                'state',
                array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates())
            )
            //For AR Payments - Don't Show AR Payment Orders in Recent Block
            ->addAttributeToFilter('ecc_arpayments_invoice',0)                
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize('5')
            ->load()
        ;

        $this->setOrders($orders);
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
