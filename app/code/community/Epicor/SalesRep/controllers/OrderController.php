<?php

require_once Mage::getModuleDir('controllers', 'Mage_Sales') . DS . 'OrderController.php';

class Epicor_SalesRep_OrderController extends Mage_Sales_OrderController
{

    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        if ($customer->isSalesRep()) {
            $customerId = $customer->getId();
            
            $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
            
            if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId || $order->getEccSalesrepCustomerId() == $customerId) && in_array($order->getState(), $availableStates, $strict = true)
            ) {
                return true;
            }
            
            return false;
        } else {
            return parent::_canViewOrder($order);
        }
    }

}
