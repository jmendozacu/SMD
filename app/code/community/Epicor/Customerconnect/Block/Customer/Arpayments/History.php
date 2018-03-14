<?php

/**
 * Order history block override
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_History extends Mage_Core_Block_Template
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customerconnect/arpayments/sales/order/history.phtml');

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('ecc_arpayments_invoice',1)
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My AR Payments'));
    }    

  protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('My AR Payments'));
        }        
        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
