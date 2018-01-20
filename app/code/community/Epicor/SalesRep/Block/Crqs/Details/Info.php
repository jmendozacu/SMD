<?php

/**
 * RFQ details - non-editable info block
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Crqs_Details_Info extends Epicor_Customerconnect_Block_Customer_Rfqs_Details_Info
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('epicor/salesrep/crqs/details/info.phtml');
        $this->setTitle($this->__('Information'));
    }

    public function getQuoteStatusHtmlSelect()
    {

        $rfq = Mage::registry('customer_connect_rfq_details');

        if (Mage::registry('rfqs_editable')) {
            $quoteStatusCode = $rfq->getQuoteStatus();

            $store_id = Mage::app()->getStore()->getStoreId();

            /* @var $model Epicor_Customerconnect_Model_Erp_Mapping_Erpquotestatus */
            $model = Mage::getModel('customerconnect/erp_mapping_erpquotestatus');

            $quoteStatusArray = $model->getCollection()->addFieldToFilter('store_id', $store_id)->getItems();
            if ($store_id != 0 && empty($quoteStatusArray)) {
                $quoteStatusArray = $model->getCollection()->addFieldToFilter('store_id', 0)->getItems();
            }

            $options = array();
            foreach ($quoteStatusArray as $quoteStatus) {
                $options[] = array(
                    'value' => $quoteStatus->getCode(),
                    'label' => $this->__($quoteStatus->getStatus())
                );
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                    ->setName('quote_status')
                    ->setId('quote-status-select')
                    ->setClass('quote-status-select')
                    ->setValue($quoteStatusCode)
                    ->setOptions($options);
            $html = $select->getHtml();
        } else {
            $html = $this->getQuoteStatus() . '<input type="hidden" id="quote_status" name="quote_status" value="' . $rfq->getQuoteStatus() . '"/>';
        }

        return $html;
    }

}
