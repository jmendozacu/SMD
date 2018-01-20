<?php

/**
 * RFQ Details page buttons
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Buttons extends Mage_Core_Block_Template
{

    private $_status;

    /**
     *
     * @var Epicor_Quotes_Model_Quote 
     */
    private $_eccQuote;

    private function _getRfqStatus()
    {
        if (!$this->_status) {
            $helper = Mage::helper('customerconnect/messaging');
            /* @var $helper Epicor_Customerconnect_Helper_Messaging */
            $rfq = Mage::registry('customer_connect_rfq_details');
            $this->_status = $helper->getErpquoteStatusDescription($rfq->getQuoteStatus(), '', 'state');
        }

        return $this->_status;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/rfqs/details/buttons.phtml');
    }

    public function showConfirm()
    {
        if (Mage::registry('hide_all_buttons')) {
            return false;
        }
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $access = $helper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'confirm', '', 'Access');

        if ($access) {
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            $access = $helper->isMessageEnabled('customerconnect', 'crqc');
            
            $erpAccount = $helper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $currencyCode = $erpAccount->getCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
            
            if ($access && !$currencyCode) {
                $access = false;
            }
        }

        return ($access && $this->confirmRejectStatusCheck());
    }

    public function showDuplicate()
    {
        if (Mage::registry('hide_all_buttons')) {
            return false;
        }
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $show = $helper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'duplicate', '', 'Access');

        if ($show) {
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            $show = $helper->isMessageEnabled('customerconnect', 'crqu');
            
            $erpAccount = $helper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $currencyCode = $erpAccount->getCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
            
            if ($show && !$currencyCode) {
                $show = false;
            }
        }
        $action = $this->getRequest()->getActionName();
        if ($action == 'new' || $action == 'duplicate') {
            $show = false;
        }

        return $show;
    }

    public function showReject()
    {
        if (Mage::registry('hide_all_buttons')) {
            return false;
        }
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $access = $helper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'reject', '', 'Access');

        if ($access) {
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            $access = $helper->isMessageEnabled('customerconnect', 'crqc');
        }

        return ($access && $this->confirmRejectStatusCheck());
    }

    private function confirmRejectStatusCheck()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');

        $status = $this->_getRfqStatus();

        return ($status == Epicor_Customerconnect_Model_Config_Source_Quotestatus::QUOTE_STATUS_AWAITING && $rfq->getQuoteEntered() == 'Y');
    }

    public function showCheckoutButton()
    {
        if (Mage::registry('hide_all_buttons')) {
            return false;
        }
        $rfq = Mage::registry('customer_connect_rfq_details');

        $status = $this->_getRfqStatus();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $show = false;

        if ($rfq->getQuoteEntered() == 'Y' && $status == Epicor_Customerconnect_Model_Config_Source_Quotestatus::QUOTE_STATUS_AWAITING) {

            if ($helper->customerHasAccess('Epicor_Quotes', 'Manage', 'accept', '', 'Access')) {

                $quote = $this->getEccQuote();

                if ($quote->getId() && $quote->isAcceptable()) {
                    $show = true;
                }
            }
        }

        return $show;
    }

    private function getEccQuote()
    {
        if (is_null($this->_eccQuote)) {
            $rfq = Mage::registry('customer_connect_rfq_details');
            $quoteNumber = $rfq->getQuoteNumber();

            $this->_eccQuote = Mage::getModel('quotes/quote');
            $this->_eccQuote->load($quoteNumber, 'quote_number');
        }

        return $this->_eccQuote;
    }

    public function getDuplicateUrl()
    {
        $parms =  $this->getRequest()->getParams();
        
        if(array_key_exists('duplicate_url', $parms)){          // if duplicate_url is specified, quote will not be and vice versa 
            return $parms['duplicate_url'];
        }else{ 
            $params = array(
                'quote' => $this->getRequest()->getParam('quote')
            );
            return Mage::getUrl('customerconnect/rfqs/duplicate', $params);
        }
    }

    public function getConfirmUrl()
    {
        return Mage::getUrl('customerconnect/rfqs/confirm');
    }

    public function getRejectUrl()
    {
        return Mage::getUrl('customerconnect/rfqs/reject');
    }

    public function getCheckoutUrl()
    {
        $quote = $this->getEccQuote();

        return Mage::getUrl('quotes/manage/accept/', array('id' => $quote->getId()));
    }

}
