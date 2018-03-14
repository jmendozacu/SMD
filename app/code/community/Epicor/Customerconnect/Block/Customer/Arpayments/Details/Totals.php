<?php
/**
 * AR Payments Payment
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Totals extends Epicor_Common_Block_Generic_Totals {

    public function _construct() {
        parent::_construct();
        $invoice = Mage::registry('customer_connect_invoices_details');

        if ($invoice) {

            $helper = Mage::helper('epicor_comm/messaging');

            $currencyCode = $helper->getCurrencyMapping($invoice->getCurrencyCode(), Epicor_Customerconnect_Helper_Data::ERP_TO_MAGENTO);

            $this->addRow('Subtotal :', $helper->getCurrencyConvertedAmount($invoice->getGoodsTotal(), $currencyCode), 'subtotal');
            $this->addRow('Shipping  &amp; Handling :', $helper->getCurrencyConvertedAmount($invoice->getCarriageAmount(), $currencyCode), 'shipping');
            
            if(!Mage::helper('epicor_comm')->removeTaxLine($invoice->getTaxAmount())){
                $this->addRow('Tax :', $helper->getCurrencyConvertedAmount($invoice->getTaxAmount(), $currencyCode));
            }   
            
            $this->addRow('Grand Total :', $helper->getCurrencyConvertedAmount($invoice->getGrandTotal(), $currencyCode), 'grand_total');
        }
        $columns = 8;
        
        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        $showLoc = ($locHelper->isLocationsEnabled()) ? $locHelper->showIn('cc_invoices') : false;

        if (!$showLoc) {
            $columns = 7;
        }
                
        // add column if lists enabled
        if(Mage::getStoreConfigFlag('epicor_lists/global/enabled')){
            $columns++;
        }
	
        
        $this->setColumns($columns);
    }

}