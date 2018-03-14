<?php
/**
 * AR Payments Admin Screen 
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */
class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        parent::_beforeToHtml();
    }

    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }
}
