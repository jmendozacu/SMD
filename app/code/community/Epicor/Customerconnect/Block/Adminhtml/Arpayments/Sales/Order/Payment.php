<?php
/**
 * AR Payments Admin Screen sales order payment information
 * @category   Epicor
 * @package    Epicor_ErpSimulator
 * @author     Epicor Websales Team
 * 
 * 
 */

class Epicor_Customerconnect_Block_Adminhtml_Arpayments_Sales_Order_Payment extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block'));
        }
        $this->setPayment($this->getParentBlock()->getOrder()->getPayment());
        parent::_beforeToHtml();
    }

    public function setPayment($payment)
    {
        $paymentInfoBlock = Mage::helper('payment')->getInfoBlock($payment);
        $this->setChild('info', $paymentInfoBlock);
        $this->setData('payment', $payment);
        return $this;
    }

    protected function _toHtml()
    {
        return $this->getChildHtml('info');
    }

}
