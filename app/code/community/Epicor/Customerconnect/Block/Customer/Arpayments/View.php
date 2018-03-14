<?php
/**
 * AR Payments Payment Sales order view block
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_Block_Customer_Arpayments_View extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/arpayments/sales/order/view.phtml');
    }

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('AR Payment Reference # %s', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getUrl('*/*/history');
        }
        return Mage::getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::helper('sales')->__('Back to My Payments');
        }
        return Mage::helper('sales')->__('View Another Payments');
    }


}
