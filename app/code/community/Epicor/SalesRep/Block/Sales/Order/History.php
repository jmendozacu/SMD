<?php

/**
 * SalesRep Order history block override
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Sales_Order_History extends Epicor_Common_Block_Order_History
{

    public function __construct()
    {
        parent::__construct();

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        if ($customer->isSalesRep()) {

            $orders = Mage::getResourceModel('sales/order_collection');
            /* @var $orders Mage_Sales_Model_Resource_Order_Collection */

            $orders->addFieldToSelect('*')
                    ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                    ->setOrder('created_at', 'desc');

            $salesRepId = $customer->getId();

            $orders->addFieldToFilter(array('customer_id' => 'customer_id', 'ecc_salesrep_customer_id' => 'ecc_salesrep_customer_id'), array('customer_id' => $salesRepId, 'ecc_salesrep_customer_id' => $salesRepId));

            $this->setOrders($orders);
        }
    }

}
