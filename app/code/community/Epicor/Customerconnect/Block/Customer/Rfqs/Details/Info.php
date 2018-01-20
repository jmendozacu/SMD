<?php

/**
 * RFQ details - non-editable info block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Info extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('customerconnect/customer/account/rfqs/details/info.phtml');
        $this->setTitle($this->__('Information'));
    }

    public function _toHtml()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');
        $html = '';

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $rfq = base64_encode(serialize($helper->varienToArray($rfq)));
        $html = '<input type="hidden" name="old_data" value="' . $rfq . '" />';

        $html .= parent::_toHtml();
        return $html;
    }
    
    public function getQuoteDate()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $date = $rfq->getQuoteDate();
        $data = '';
        
        if (!empty($date)) {
            try {
                $data = $helper->getLocalDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            } catch (Exception $ex) {
                $data = $date;
            }
        }

        return $data;
    }

    public function getQuoteStatus()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');

        $helper = Mage::helper('customerconnect/messaging');
        /* @var $helper Epicor_Customerconnect_Helper_Messaging */
        return $helper->getErpquoteStatusDescription($rfq->getQuoteStatus());
    }

}
