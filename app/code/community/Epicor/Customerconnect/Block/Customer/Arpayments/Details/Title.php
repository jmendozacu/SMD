<?php

/**
 * Invoice Details page title
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Title
        extends Epicor_Customerconnect_Block_Customer_Title
{
    
    protected $_reorderType = 'Invoices';
    protected $_returnType = 'Invoice';

    public function _construct()
    {
        parent::_construct();
        $this->_setTitle();
        $this->_setReorderUrl();
        $this->_setReturnUrl();
        $this->setTemplate('customerconnect/arpayments/invoices/title.phtml');
    }

    /**
     * Sets the page title
     */
    protected function _setTitle()
    {
        $invoice = Mage::registry('customer_connect_invoices_details');
        $this->_title = $this->__('Invoice Number : %s', $invoice->getInvoiceNumber());
    }

    /**
     * Sets the Reorder link url
     */
    protected function _setReorderUrl()
    {
        $invoice = Mage::registry('customer_connect_invoices_details');

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $this->_reorderUrl = $helper->getInvoiceReorderUrl($invoice, Mage::helper('core/url')->getCurrentUrl());
    }
    
    /**
     * Sets the Return link url
     */
    protected function _setReturnUrl()
    {
        $invoice = Mage::registry('customer_connect_invoices_details');

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $this->_returnUrl = $helper->getInvoiceReturnUrl($invoice);
    }

}
