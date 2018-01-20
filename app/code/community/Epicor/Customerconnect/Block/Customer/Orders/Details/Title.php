<?php

/**
 * Order Details page title
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Orders_Details_Title
        extends Epicor_Customerconnect_Block_Customer_Title
{

    protected $_reorderType = 'Orders';
    protected $_returnType = 'Order';

    public function _construct()
    {
        parent::_construct();
        $this->_setTitle();
        $this->_setReorderUrl();
        $this->_setReturnUrl();
    }

    /**
     * Sets the page title
     */
    protected function _setTitle()
    {
        $order = Mage::registry('customer_connect_order_details');
        $this->_title = $this->__('Order Number : %s', $order->getOrderNumber());
    }

    /**
     * Sets the Reorder link url
     */
    protected function _setReorderUrl()
    {
        $order = Mage::registry('customer_connect_order_details');

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $this->_reorderUrl = $helper->getOrderReorderUrl($order, Mage::helper('core/url')->getCurrentUrl());
    }
    
    /**
     * Sets the Return link url
     */
    protected function _setReturnUrl()
    {
        $order = Mage::registry('customer_connect_order_details');

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $this->_returnUrl = $helper->getOrderReturnUrl($order);
    }

}
