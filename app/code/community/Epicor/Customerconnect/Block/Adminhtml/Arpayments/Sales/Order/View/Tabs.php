<?php
/**
 * AR Payments Admin Screen sales Order view tabs
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */

class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        Mage::throwException(Mage::helper('sales')->__('Cannot get the payment instance.'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_view_tabs');
        $this->setDestElementId('sales_order_view');
        $this->setTitle(Mage::helper('sales')->__('AR Payments View'));
    }

}
