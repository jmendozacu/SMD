<?php

/**
 * RFQ line totals display
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Totals extends Epicor_Common_Block_Generic_Totals
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('customerconnect/customer/account/rfqs/details/totals.phtml');

        $rfq = Mage::registry('customer_connect_rfq_details');

        if ($rfq) {

            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */

            $options = array(
                //'display' => ''
            );

            $currencyCode = $helper->getCurrencyMapping(
                $rfq->getCurrencyCode(), Epicor_Customerconnect_Helper_Data::ERP_TO_MAGENTO
            );

            $this->addRow(
                'Subtotal :', $helper->getCurrencyConvertedAmount($rfq->getGoodsTotal(), $currencyCode, null, $options),
                'subtotal'
            );
            $this->addRow(
                'Shipping  &amp; Handling :',
                $helper->getCurrencyConvertedAmount($rfq->getCarriageAmount(), $currencyCode, null, $options),
                'shipping'
            );
            if (Mage::getStoreConfig('Epicor_Comm/licensing/erp') != 'e10') {                
                
                 if(!Mage::helper('epicor_comm')->removeTaxLine($rfq->getTaxAmount())){     // only display tax line if config value is set
                    $this->addRow(
                        'Tax :', $helper->getCurrencyConvertedAmount($rfq->getTaxAmount(), $currencyCode, null, $options),
                        'tax'
                    );
                }  
            }

            $this->addRow(
                'Grand Total :',
                $helper->getCurrencyConvertedAmount($rfq->getGrandTotal(), $currencyCode, null, $options), 'grand_total'
            );
        }

        if (Mage::registry('rfqs_editable')) {
            $this->setColumns(10);
        } else {
            $this->setColumns(9);
        }       
    }

}
