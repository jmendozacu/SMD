<?php

/**
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Checkout_Multishipping_Overview extends Mage_Checkout_Block_Multishipping_Overview
{
    /**
     * Initialize default item renderer for row-level items output
     */
    protected $_originalAddressTotals = array();
    protected function _construct()
    {
        parent::_construct();
        $this->addItemRender(
            $this->_getRowItemType('default'),
            'checkout/cart_item_renderer',
            'epicor_comm/checkout/multishipping/overview/item.phtml'
        );
    }    
    
    public function getShippingAddressTotals($address)
    {
        $totals = $address->getTotals();  
           
        if(Mage::helper('epicor_comm')->removeTaxLine($totals['tax']->getValue())){
            unset($totals['tax']);
        } 
        foreach ($totals as $total) {
            if ($total->getCode()=='grand_total') {
                if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING) {
                    $total->setTitle($this->__('Total'));
                }
                else {
                    $total->setTitle($this->__('Total for this address'));
                }
            }
        }
        return $totals;
    }
}