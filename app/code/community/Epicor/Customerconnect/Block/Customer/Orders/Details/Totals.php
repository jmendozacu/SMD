<?php

class Epicor_Customerconnect_Block_Customer_Orders_Details_Totals extends Epicor_Common_Block_Generic_Totals {

    public function _construct() {
        parent::_construct();
        $order = Mage::registry('customer_connect_order_details');

        if ($order) {

            $helper = Mage::helper('epicor_comm/messaging');

            $currencyCode = $helper->getCurrencyMapping($order->getCurrencyCode(), Epicor_Customerconnect_Helper_Data::ERP_TO_MAGENTO);

            $this->addRow('Subtotal :', $helper->getCurrencyConvertedAmount($order->getGoodsTotal(), $currencyCode), 'subtotal');
            
            $this->addRow('Shipping  &amp; Handling :', $helper->getCurrencyConvertedAmount($order->getCarriageAmount(), $currencyCode), 'shipping');
            
            if(!Mage::helper('epicor_comm')->removeTaxLine($order->getTaxAmount())){    
                $this->addRow('Tax :', $helper->getCurrencyConvertedAmount($order->getTaxAmount(), $currencyCode));
            }
            
            $this->addRow('Grand Total :', $helper->getCurrencyConvertedAmount($order->getGrandTotal(), $currencyCode), 'grand_total');
        }

        $columns = 9;
        
        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        $showLoc = ($locHelper->isLocationsEnabled()) ? $locHelper->showIn('cc_orders') : false;

        if (!$showLoc) {
            $columns = 8;
        }
        if(Mage::getStoreConfigFlag('epicor_lists/global/enabled')){
            $columns++;
        }
        
        $this->setColumns($columns);
    }

}