<?php

/**
 * Account controller
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_CrqsController extends Epicor_SalesRep_Controller_Abstract
{
    protected $_quoteDetails;
    /**
     * Index action 
     */
    public function indexAction()
    {
        $helper = Mage::helper('epicor_comm/messaging_crqs');
        /* @var $helper Epicor_Comm_Helper_Messaging_Crqs */
        
        if ($helper->mutipleAccountsEnabled()) {
            $crqs = Mage::getSingleton('customerconnect/message_request_crqs');
            $messageTypeCheck = $crqs->getHelper('customerconnect/messaging')->getMessageType('CRQS');
            if ($crqs->isActive() && $messageTypeCheck) {
                Mage::register('rfqs_editable', true);
                $this->loadLayout()->renderLayout();
            } else {
                Mage::getSingleton('core/session')->addError('ERROR - RFQ Search not available');
                if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                    session_write_close();
                    $this->_redirect('customer/account/index');
                }
            }
        } else {
            $this->norouteAction();
        }
    }

    public function detailsAction()
    {
        if ($this->_loadRfq()) {
            $this->loadLayout()->renderLayout();
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }

    private function _loadRfq()
    {
        $newRfq = Mage::getSingleton('customer/session')->getNewRfq();

        $session = Mage::getSingleton('core/session');
        /* @var $session Mage_Core_Model_Session */

        $loaded = false;

        if ($newRfq) {
            Mage::register('customer_connect_rfq_details', $newRfq);
            Mage::getSingleton('customer/session')->unsNewRfq();
            $loaded = true;
        }

        if (!Mage::registry('customer_connect_rfq_details')) {

            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $quote = $helper->urlDecode(Mage::app()->getRequest()->getParam('quote'));
            $quoteDetails = unserialize($helper->decrypt($quote));
            if (isset($quoteDetails['return'])) {
                Mage::register('rfq_return_url', $quoteDetails['return']);
                unset($quoteDetails['return']);
            }

            $brand = $helper->getStoreBranding();
            
            $accountNumber = $quoteDetails['erp_account'];
            // if brand company exists, check if brand and delimiter are already in the erp_account, if not add them
            if ($brand->getCompany()) {
                if (!strstr($quoteDetails['erp_account'], $brand->getCompany() . $helper->getUOMSeparator())) {
                    $accountNumber = $brand->getCompany() . $helper->getUOMSeparator() . $quoteDetails['erp_account'];
                }
            }
            $erpAccount = $helper->getErpAccountByAccountNumber($accountNumber);
            Mage::register('crq_erp_account', $erpAccount);
            if (
                    count($quoteDetails) == 3 && !empty($quoteDetails['quote_number']) && array_key_exists('quote_sequence', $quoteDetails)
            ) {
                $crqd = Mage::getSingleton('customerconnect/message_request_crqd');
                $messageTypeCheck = $crqd->getHelper('customerconnect/messaging')->getMessageType('CRQD');

                if ($crqd->isActive() && $messageTypeCheck) {

                    $crqd->setAccountNumber($quoteDetails['erp_account'])
                            ->setQuoteNumber($quoteDetails['quote_number'])
                            ->setQuoteSequence($quoteDetails['quote_sequence'])
                            ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                    if ($crqd->sendMessage()) {
                        $rfq = $crqd->getResults();
                        Mage::register('customer_connect_rfq_details', $rfq);
                        $loaded = true;
                    } else {
                        $session->addError('Failed to retrieve RFQ Details');
                    }
                } else {
                    $session->addError('ERROR - RFQ Details not available');
                }
            } else {
                $session->addError('ERROR - Invalid RFQ Number');
            }
        } else {
            $loaded = true;
        }

        if ($loaded) {
            $accessHelper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */
            $editable = $accessHelper->customerHasAccess('Epicor_SalesRep', 'Crqs', 'update', '', 'Access');

            $helper = Mage::helper('customerconnect/messaging');
            /* @var $helper Epicor_Customerconnect_Helper_Messaging */
            $rfq = Mage::registry('customer_connect_rfq_details');
            $status = $helper->getErpquoteStatusDescription($rfq->getQuoteStatus(), '', 'state');

            if ($editable) {
                if ($status != Epicor_Customerconnect_Model_Config_Source_Quotestatus::QUOTE_STATUS_PENDING) {
                    $editable = false;
                }
            }

            $msgHelper = Mage::helper('epicor_comm/messaging');
            /* @var $msgHelper Epicor_Comm_Helper_Messaging */
            
            if ($editable && $rfq->getCurrencyCode() != $msgHelper->getCurrencyMapping()) {
                $editable = false;
            }
            
            $enabled = $msgHelper->isMessageEnabled('customerconnect', 'crqu');

            if ($enabled && $status == Epicor_Customerconnect_Model_Config_Source_Quotestatus::QUOTE_STATUS_AWAITING) {
                Mage::register('rfqs_editable_partial', true);
            }

            if (!$enabled) {
                $editable = false;
            }

            if ($erpAccount->getErpCode() !== $helper->getErpAccountNumber()) {
                $editable = false;
                $returnUrl = Mage::helper('epicor_comm')->urlEncode(Mage::helper('core/url')->getCurrentUrl());
                $masqUrl = Mage::getUrl('epicor_comm/masquerade/masquerade', array('masquerade_as' => $erpAccount->getId(), 'return_url' => $returnUrl));
                $session->addNotice($this->__('You are not masquerading as the ERP Account for this Quote'));

                $customerSession = Mage::getSingleton('customer/session');
                /* @var $customerSession Mage_Customer_Model_Session */

                $customer = $customerSession->getCustomer();
                /* @var $customer Epicor_Comm_Model_Customer */

                if ($customer->canMasqueradeAs($erpAccount->getId())) {
                    $session->addNotice($this->__('In order to make changes to this Quote, you must be masquerading as the correct ERP Account. %s', '<a href="' . $masqUrl . '">Start Masquerade Now</a>'));
                } else {
                    $session->addNotice($this->__('You are not allowed to masquerade as this ERP Account'));
                }
                Mage::register('hide_all_buttons', true);
            }

            Mage::register('rfqs_editable', $editable);
        }

        return $loaded;
    }

    public function updateAction()
    {
//        $salesrepHelper = Mage::helper('epicor_salesrep');
//        $erpAccount = $salesrepHelper->getErpAccountByAccountNumber($accountNumber);
//        $masqueradeErpAccount =  
        $files = array();

        $error = '';

        try {
            $rfqs = $this->getRequest()->getParam('rfq_serialize_data');
            if ($newData =  Mage::helper('customerconnect/rfq')->getProcessedRfqData($rfqs)) {

                $helper = Mage::helper('customerconnect/rfq');
                /* @var $helper Epicor_Customerconnect_Helper_Rfq */
                $fileHelper = Mage::helper('epicor_common/file');
                /* @var $fileHelper Epicor_Common_Helper_File */
                $prpHelper = Mage::helper('epicor_salesrep/pricing_rule_product');
                /* @var $prpHelper Epicor_SalesRep_Helper_Pricing_Rule_Product */
                $commHelper = Mage::helper('epicor_comm');
                /* @var $commHelper Epicor_Comm_Helper_Data */
                $oldData = unserialize(base64_decode($newData['old_data']));
                unset($newData['old_data']);
                $newData = $commHelper->sanitizeData($newData);
                $oldData = $commHelper->sanitizeData($oldData);

                $crqu = Mage::getSingleton('customerconnect/message_request_crqu');
                /* @var $crqu Epicor_Customerconnect_Model_Message_Request_Crqu */

                if ($crqu->isActive() && $helper->getMessageType('CRQU')) {

                    $aFiles = array();
                    $lFiles = array();

                    if (isset($newData['attachments'])) {
                        $aFiles = $fileHelper->processPageFiles('attachments', $newData);
                    }

                    if (isset($newData['lineattachments'])) {
                        $lFiles = $fileHelper->processPageFiles('lineattachments', $newData);
                    }

                    $files = array_merge($aFiles, $lFiles);

                    $crqu->setAction('U');
                    $crqu->setQuoteNumber($newData['quote_number']);
                    $crqu->setQuoteSequence($newData['quote_sequence']);
                    $crqu->setOldData($oldData);
                    $crqu->setNewData($newData);
                    
                    $failedProducts = $prpHelper->validateLinesForDiscountedPrices($newData['lines']);
                    
                    if(count($failedProducts) == 0){
                        if ($crqu->sendMessage()) {
                            Mage::getSingleton('core/session')->addSuccess($this->__('RFQ update request sent successfully'));

                            Mage::register('rfqs_editable', true);

                            $rfq = $crqu->getResults();

                            $helper->processCrquFilesSuccess($files, $rfq);

                            Mage::register('customer_connect_rfq_details', $rfq);
                        } else {

                            $helper->processCrquFilesFail($files);
                            $error = $this->__('RFQ update request failed');
                        }
                    }elseif (count($failedProducts) == 1) {
                        $error = $this->__('Product %s has an invalid price', implode(', ', $failedProducts));
                    }else{
                        $error = $this->__('Products %s have an invalid price', implode(', ', $failedProducts));
                    }
                } else {
                    $error = $this->__('RFQ update not available');
                }
            } else {
                $error = $this->__('No Data Sent');
            }
        } catch (Exception $ex) {
            $error = $this->__('An error occurred, please try again');
            Mage::logException($ex);
        }

        if ($error) {
            Mage::register('rfq_error', $error);
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_showerror')->toHtml()
            );
        } else {
            $cusHelper = Mage::helper('customerconnect');
            /* @var $cusHelper Epicor_Customerconnect_Helper_Data */
            $erpAccountNum = $cusHelper->getErpAccountNumber();

            $quoteDetails = array(
                'erp_account' => $erpAccountNum,
                'quote_number' => $rfq->getQuoteNumber(),
                'quote_sequence' => $rfq->getQuoteSequence()
            );
            $requested = $helper->urlEncode($helper->encrypt(serialize($quoteDetails)));
            $url = Mage::getUrl('*/*/details', array('quote' => $requested));
            
            Mage::register('rfq_redirect_url', $url);

            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_redirector')->toHtml()
            );
        }
    }

    /**
     * Export CRQ grid to CSV format
     */
    public function exportCsvAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = $baseUrl . 'crq.csv';
        $content = $this->getLayout()->createBlock('epicor_salesrep/crqs_list_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invoices grid to XML format
     */
    public function exportXmlAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = $baseUrl . 'crq.xml';
        $content = $this->getLayout()->createBlock('epicor_salesrep/crqs_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

}
