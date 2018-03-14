<?php
/**
 * AR Payments Admin Screen 
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */
class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_View_Tab_Transactions extends Mage_Adminhtml_Block_Sales_Transactions_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/sales_order/transactions', array(
            '_current' => true
        ));
    }
    
    /**
     * Retrieve grid row url
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/sales_transactions/view', array(
            '_current' => true,
            'txn_id' => $item->getId()
        ));
    }
    
    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Transactions');
    }
    
    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Transactions');
    }
    
    /**
     * Check whether can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return false;
    }
    
    /**
     * Check whether tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return !Mage::getSingleton('admin/session')->isAllowed('sales/transactions/fetch');
    }
}
