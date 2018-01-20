<?php

/**
 * RFQ details page title
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Title extends Epicor_Customerconnect_Block_Customer_Title
{

    public function _construct()
    {
        parent::_construct();
        $this->_setTitle();
    }

    /**
     * Sets the page title
     */
    protected function _setTitle()
    {
        if (Mage::registry('rfq_new')) {
            $this->_title = $this->__('New Quote');
        } else {
            $order = Mage::registry('customer_connect_rfq_details');
            $this->_title = $this->__('Quote Number : %s', $order->getQuoteNumber());
        }
    }

    /**
     * Returns whether an entity can be reordered or not
     * 
     * @return boolean
     */
    public function canReorder()
    {
        return false;
    }
    /**
     * Returns whether an entity can be reordered or not
     * 
     * @return boolean
     */
    public function canReturn()
    {
        return false;
    }

}
