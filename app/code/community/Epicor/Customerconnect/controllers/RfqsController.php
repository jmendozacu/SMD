<?php

/**
 * RFQs controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_RfqsController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $crqs = Mage::getSingleton('customerconnect/message_request_crqs');
        $messageTypeCheck = $crqs->getHelper('customerconnect/messaging')->getMessageType('CRQS');

        if ($crqs->isActive() && $messageTypeCheck) {
            $accessHelper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */
            $access = $accessHelper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'confirmreject', '', 'Access');
            Mage::register('rfqs_editable', $access);
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('ERROR - RFQ Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    /**
     * Confirm / reject new submit action
     */
    public function confirmrejectAction()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            
            $data = $commHelper->sanitizeData($data);

            $message = Mage::getSingleton('customerconnect/message_request_crqc');
            /* @var $message Epicor_Customerconnect_Model_Message_Request_Crqc */

            $messageTypeCheck = $message->getHelper('customerconnect/messaging')->getMessageType('CRQC');

            if ($message->isActive() && $messageTypeCheck) {

                if (empty($data['confirmed']) && empty($data['rejected'])) {
                    Mage::getSingleton('core/session')->addError('No RFQs selected');
                } else {
                    $message->setRfqData($data['rfq']);

                    if (isset($data['confirmed']) && !empty($data['confirmed'])) {
                        $message->setConfirmed($data['confirmed']);
                    }

                    if (isset($data['rejected']) && !empty($data['rejected'])) {
                        $message->setRejected($data['rejected']);
                    }

                    if ($message->sendMessage()) {
                        Mage::getSingleton('core/session')->addSuccess($this->__('RFQs processed successfully'));
                    } else {
                        Mage::getSingleton('core/session')->addError($this->__('Failed to process RFQs '));
                    }
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('RFQ updating not available'));
            }

            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('*/*/index');
            }
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

    public function confirmAction()
    {
        $data = $this->getRequest()->getPost();

        $response = json_encode(array('message' => $this->__('No Data Sent'), 'type' => 'error'));

        if ($data) {
            $helper = Mage::helper('customerconnect/rfq');
            /* @var $helper Epicor_Customerconnect_Helper_Rfq */
            
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            
            $data = $commHelper->sanitizeData($data);

            $response = $helper->processRfqCrqc('confirm', $data, $response);
        }

        $this->getResponse()->setBody($response);
    }

    public function rejectAction()
    {
        $data = $this->getRequest()->getPost();

        $response = json_encode(array('message' => $this->__('No Data Sent'), 'type' => 'error'));

        if ($data) {
            $helper = Mage::helper('customerconnect/rfq');
            /* @var $helper Epicor_Customerconnect_Helper_Rfq */
            
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            
            $data = $commHelper->sanitizeData($data);

            $response = $helper->processRfqCrqc('reject', $data, $response);
        }

        $this->getResponse()->setBody($response);
    }

    public function newAction()
    {
        $rfq = $this->_initNewRfq();
        Mage::register('customer_connect_rfq_details', $rfq);

        $this->loadLayout()->renderLayout();
        
        $erpAccount = Mage::helper('epicor_comm')->getErpAccountInfo();
        $currencyCode = $erpAccount->getCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());

        if (Mage::getSingleton('core/session')->getMessages()->getItems() || !$currencyCode) {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }

    public function addAction()
    {
        $error = false;
        $deliveryAddress = $this->getRequest()->getPost('delivery_address');
        $error =  $this->getRequest()->getPost('delivery_address') ? Mage::helper('customerconnect')->addressValidate($deliveryAddress, true) : null; 
        if(!isset($error)){
            try {
                if ($newData = $this->getRequest()->getPost()) {
                    $helper = Mage::helper('customerconnect/rfq');
                    /* @var $helper Epicor_Customerconnect_Helper_Rfq */
                    $fileHelper = Mage::helper('epicor_common/file');
                    /* @var $fileHelper Epicor_Common_Helper_File */
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    /* @var $customer Epicor_Comm_Model_Customer */
                    $commHelper = Mage::helper('epicor_comm');
                    /* @var $commHelper Epicor_Comm_Helper_Data */
                    $oldData = unserialize(base64_decode($newData['old_data']));
                    unset($newData['old_data']);
                    $newData = $commHelper->sanitizeData($newData);
                    $oldData = $commHelper->sanitizeData($oldData);

                    $crqu = Mage::getSingleton('customerconnect/message_request_crqu');
                    /* @var $crqu Epicor_Customerconnect_Model_Message_Request_Crqu */
                    $duplicate = isset($newData['is_duplicate']) ? true : false;

                    if ($crqu->isActive() && $helper->getMessageType('CRQU')) {

                        if ($customer->isSalesRep()) {
                            $prpHelper = Mage::helper('epicor_salesrep/pricing_rule_product');
                            /* @var $prpHelper Epicor_SalesRep_Helper_Pricing_Rule_Product */
                            $failedProducts = $prpHelper->validateLinesForDiscountedPrices($newData['lines']);
                        } else {
                            $failedProducts = array();
                        }

                        if (count($failedProducts) == 0) {

                            $aFiles = array();
                            $lFiles = array();

                            if (isset($newData['attachments'])) {
                                $aFiles = $fileHelper->processPageFiles('attachments', $newData, $duplicate, false);
                            }

                            if (isset($newData['lineattachments'])) {
                                $lFiles = $fileHelper->processPageFiles('lineattachments', $newData, $duplicate, false);
                            }

                            if (Mage::registry('download_erp_files')) {
                                Mage::getSingleton('core/session')->addSuccess($this->__('New RFQ request sent. There will be a delay while attachments are synced'));
                                $connection = new Zend_Http_Client();
                                $adapter = new Zend_Http_Client_Adapter_Curl();

                                try {
                                    $connection->setUri(Mage::getUrl('epicor_comm/message/crqu', array('_store' => Mage::app()->getStore()->getId())));

                                    $adapter->setCurlOption(CURLOPT_RETURNTRANSFER, 0);
                                    $adapter->setCurlOption(CURLOPT_POST, 1);

                                    $adapter->setCurlOption(CURLOPT_USERAGENT, 'api');
                                    $adapter->setCurlOption(CURLOPT_TIMEOUT, 1);
                                    $adapter->setCurlOption(CURLOPT_HEADER, 0);
                                    $adapter->setCurlOption(CURLOPT_RETURNTRANSFER, false);
                                    $adapter->setCurlOption(CURLOPT_FORBID_REUSE, true);
                                    $adapter->setCurlOption(CURLOPT_CONNECTTIMEOUT, 1);
                                    $adapter->setCurlOption(CURLOPT_DNS_CACHE_TIMEOUT, 10);
                                    $adapter->setCurlOption(CURLOPT_FRESH_CONNECT, true);

                                    $helper = Mage::helper('customerconnect');
                                    /* @var $helper Epicor_Customerconnect_Helper_Data */

                                    $newData['account_number'] = $helper->getErpAccountNumber();

                                    $connection->setParameterPost('data', base64_encode(serialize($newData)));
                                    $connection->setAdapter($adapter);
                                    $connection->request(Zend_Http_Client::POST);
                                } catch (Exception $e) {

                                }
                                $url = Mage::getUrl('*/*/index');

                                Mage::register('rfq_redirect_url', $url);

                                $this->getResponse()->setBody(
                                        $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_redirector')->toHtml()
                                );

                                return;
                            } else {
                                $files = array_merge($aFiles, $lFiles);

                                $crqu->setAction('A');
                                $crqu->setQuoteNumber('');
                                $crqu->setQuoteSequence('');
                                $crqu->setOldData(array());
                                $crqu->setNewData($newData);

                                if ($crqu->sendMessage()) {
                                    Mage::getSingleton('core/session')->addSuccess($this->__('New RFQ request sent successfully'));

                                    $accessHelper = Mage::helper('epicor_common/access');
                                    /* @var $helper Epicor_Common_Helper_Access */

                                    $access = $accessHelper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'update', '', 'Access');
                                    Mage::register('rfqs_editable', $access);

                                    $rfq = $crqu->getResults();

                                    $helper->processCrquFilesSuccess($files, $rfq);
                                    Mage::getSingleton('customer/session')->setNewRfq($rfq);
                                } else {
                                    $helper->processCrquFilesFail($files);
                                    $error = $this->__('RFQ add request failed');
                                }
                            }
                        } elseif (count($failedProducts) == 1) {
                            $error = $this->__('Product %s has an invalid price', implode(', ', $failedProducts));
                        } else {
                            $error = $this->__('Products %s have an invalid price', implode(', ', $failedProducts));
                        }
                    } else {
                        $error = $this->__('RFQ add not available');
                    }
                } else {
                    $error = $this->__('No Data Sent');
                }
            } catch (Exception $ex) {
                $error = $this->__('An error occurred, please try again:' . $ex->getMessage());
            }
        }
        if ($error) {
            Mage::register('rfq_error', $error);

            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_showerror')->toHtml()
            );
        } else {

            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNum = $helper->getErpAccountNumber();

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

    public function updateAction()
    {
        $files = array();

        $error = '';        
        $deliveryAddress = $this->getRequest()->getPost('delivery_address');
        $error =  $this->getRequest()->getPost('delivery_address') ? Mage::helper('customerconnect')->addressValidate($deliveryAddress, true) : null; 
        if(!isset($error)){
            try {
                if ($newData = $this->getRequest()->getPost()) {

                    $helper = Mage::helper('customerconnect/rfq');
                    /* @var $helper Epicor_Customerconnect_Helper_Rfq */
                    $fileHelper = Mage::helper('epicor_common/file');
                    /* @var $fileHelper Epicor_Common_Helper_File */
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    /* @var $customer Epicor_Comm_Model_Customer */
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

                        if ($customer->isSalesRep()) {
                            $prpHelper = Mage::helper('epicor_salesrep/pricing_rule_product');
                            /* @var $prpHelper Epicor_SalesRep_Helper_Pricing_Rule_Product */
                            $failedProducts = $prpHelper->validateLinesForDiscountedPrices($newData['lines']);
                        } else {
                            $failedProducts = array();
                        }

                        if (count($failedProducts) == 0) {
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
                        } elseif (count($failedProducts) == 1) {
                            $error = $this->__('Product %s has an invalid price', implode(', ', $failedProducts));
                        } else {
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
            }
        }    
        if ($error) {
            Mage::register('rfq_error', $error);

            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_showerror')->toHtml()
            );
        } else {
            $this->loadLayout()->renderLayout();
        }
    }

    public function duplicateAction()
    {
        if ($this->_loadRfq()) {
            $origRfq = Mage::registry('customer_connect_rfq_details');
            Mage::unregister('customer_connect_rfq_details');
            Mage::unregister('rfqs_editable');

            $newRfq = $this->_initNewRfq();

            $helper = Mage::helper('customerconnect/rfq');
            /* @var $helper Epicor_Customerconnect_Helper_Rfq */

            $errors = $helper->duplicateLines($origRfq, $newRfq);
            Mage::register('customer_connect_rfq_details', $newRfq);

            if ($errors) {
                foreach ($errors as $productCode) {
                    Mage::getSingleton('core/session')->addError($this->__('Product %s Could not be duplicated, not currently available', $productCode));
                }
            }

            $this->loadLayout()->renderLayout();
        } else {
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('*/*/index');
            }
        }
    }

    public function addressdetailsAction()
    {

        $addressId = Mage::app()->getRequest()->getParam('addressid');
        $type = Mage::app()->getRequest()->getParam('type');

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        if ($addressId) {
            if (strpos($addressId, 'erpaddress_') !== false) {
                $addressId = str_replace('erpaddress_', '', $addressId);

                $erpAddress = Mage::getModel('epicor_comm/customer_erpaccount_address')->load($addressId);
                /* @var $erpAddress Epicor_Comm_Model_Customer_Erpaccount_Address */

                $address = $erpAddress->toCustomerAddress($customer);
            } else {
                $address = $customer->getAddressById($addressId);
            }

            $content = $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_addressdetails')
                            ->setAddressType($type)
                            ->setAddressFromCustomerAddress($address)->toHtml();
        } else {
            $addressParam = Mage::app()->getRequest()->getParam('address-data');
            $addressData = !empty($addressParam) ? (array) json_decode($addressParam) : array();

            $content = $this->getLayout()->createBlock('customerconnect/customer_editableaddress')
                            ->setAddressType($type)
                            ->setFieldnamePrefix($type . '_address[')
                            ->setFieldnameSuffix(']')
                            ->setShowAddressCode(false)
                            ->setAddressFromCustomerAddress(new Varien_Object($addressData))->toHtml();
        }

        $this->getResponse()->setBody($content);
    }

    /**
     * Export RFQ grid to CSV format
     */
    public function exportCsvAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = $baseUrl . '_rfq.csv';
        $content = $this->getLayout()->createBlock('customerconnect/customer_rfqs_list_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invoices grid to XML format
     */
    public function exportXmlAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = $baseUrl . '_rfq.xml';
        $content = $this->getLayout()->createBlock('customerconnect/customer_rfqs_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    private function _loadRfq()
    {
        $newRfq = Mage::getSingleton('customer/session')->getNewRfq();

        $loaded = false;

        if ($newRfq) {
            Mage::register('customer_connect_rfq_details', $newRfq);
            Mage::getSingleton('customer/session')->unsNewRfq();
            $loaded = true;
        }

        if (!Mage::registry('customer_connect_rfq_details')) {

            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNumber = $helper->getErpAccountNumber();
            $quote = $helper->urlDecode(Mage::app()->getRequest()->getParam('quote'));
            $quoteDetails = unserialize($helper->decrypt($quote));
            if (isset($quoteDetails['return'])) {
                Mage::register('rfq_return_url', $quoteDetails['return']);
                unset($quoteDetails['return']);
            }

            if (
                    count($quoteDetails) == 3 &&
                    $quoteDetails['erp_account'] == $erpAccountNumber &&
                    !empty($quoteDetails['quote_number']) && array_key_exists('quote_sequence', $quoteDetails)
            ) {
                $crqd = Mage::getSingleton('customerconnect/message_request_crqd');
                $messageTypeCheck = $crqd->getHelper('customerconnect/messaging')->getMessageType('CRQD');

                if ($crqd->isActive() && $messageTypeCheck) {

                    $crqd->setAccountNumber($erpAccountNumber)
                            ->setQuoteNumber($quoteDetails['quote_number'])
                            ->setQuoteSequence($quoteDetails['quote_sequence'])
                            ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                    if ($crqd->sendMessage()) {
                        $rfq = $crqd->getResults();
                        Mage::register('customer_connect_rfq_details', $rfq);
                        $loaded = true;
                    } else {
                        Mage::getSingleton('core/session')->addError($this->__('Failed to retrieve RFQ Details'));
                    }
                } else {
                    Mage::getSingleton('core/session')->addError($this->__('ERROR - RFQ Details not available'));
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('ERROR - Invalid RFQ Number'));
            }
        } else {
            $loaded = true;
        }

        if ($loaded) {
            $accessHelper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */
            $editable = $accessHelper->customerHasAccess(
                    'Epicor_Customerconnect', 'Rfqs', 'update', '', 'Access'
            );

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

            Mage::register('rfqs_editable', $editable);
        }

        return $loaded;
    }

    public function lineaddautocompleteAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_comm/cart_quickadd_autocomplete')->toHtml());
    }

    public function linesearchAction()
    {
        foreach ($this->getRequest()->getParams() as $key => $value) {
            if (substr($key, 0, 4) == 'amp;')
                $this->getRequest()->setParam(substr($key, 4), $value);
        }

        $q = $this->getRequest()->getParam('q', '');
        $instock = $this->getRequest()->getParam('instock', '');
        Mage::register('search-query', $q);

        if (!empty($q)) {

//        Mage::register('search-sku', $sku);
            Mage::register('search-instock', $instock != '' ? true : false);

            $query = Mage::helper('catalogsearch')->getQuery();
            /* @var $query Mage_CatalogSearch_Model_Query */

            $query->setStoreId(Mage::app()->getStore()->getId());
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }

                $query->prepare();
            }

            Mage::helper('catalogsearch')->checkNotes();

            if (!empty($q) && !Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        }


        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function importProductCsvAction()
    {
        if (empty($_FILES['import_product_csv_file']['tmp_name'])) {
            Mage::getSingleton('core/session')->addNotice($this->__('Please select a file before submitting'));
            $this->_redirectReferer();
            return;
        }

        $productHelper = Mage::helper('epicor_comm/product');
        /* @var $productHelper Epicor_Comm_Helper_Product */

        $data = $this->getRequest()->getPost();

        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        $products = $helper->processProductCsvUpload($_FILES['import_product_csv_file']['tmp_name']);

        $prodArray = array();

        if (!empty($products['products'])) {
            $messenger = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */

            $msqProds = array();
            $qty = array();
            foreach ($products['products'] as $x => $product) {
                $msqProds[$x] = $product['product_added'];
                $qty[$x] = $product['qty'];
            }

            $messenger->sendMsq($msqProds);

            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */

            $customer = $customerSession->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */


            foreach ($msqProds as $index => $product) {
                /* @var $product Epicor_Comm_Model_Product */
                $skuQty = (is_array($qty)) ? $qty[$index] : 1;
                $price = $product->unsFinalPrice()->getFinalPrice($skuQty);
                $formattedPrice = $helper->formatPrice($price, true, Mage::app()->getStore()->getBaseCurrencyCode());
                $formattedTotal = $helper->formatPrice($price * $skuQty, true, Mage::app()->getStore()->getBaseCurrencyCode());
                $product->setFormattedPrice($formattedPrice);
                $product->setFormattedTotal($formattedTotal);
                $product->setMsqQty($skuQty);
                $product->setQty($skuQty);
                $product->setUsePrice($price);
                $prodArray[] = $productHelper->getProductInfoArray($product);
            }
        }

        if (!empty($products['errors'])) {
            foreach ($products['errors'] as $error) {
                Mage::getSingleton('core/session')->addError($error);
            }
        }

        $response = array(
            'errors' => $products['errors'],
            'products' => $prodArray
        );

        Mage::register('line_add_json', json_encode($response));

        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_lineaddbyjs')->toHtml()
        );
    }

    public function ewaeditcompleteAction()
    {
        Mage::register('line_edit', true);
        $this->_ewaProcess();
    }

    private function _ewaProcess()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */
        $productHelper = Mage::helper('epicor_comm/product');
        /* @var $productHelper Epicor_Comm_Helper_Product */

        $ewaCode = $helper->urlDecode(Mage::app()->getRequest()->getParam('EWACode'));
        $groupSequence = $helper->urlDecode(Mage::app()->getRequest()->getParam('GroupSequence'));
        $productSku = $helper->urlDecode(Mage::app()->getRequest()->getParam('SKU'));
        $qty = $helper->urlDecode(Mage::app()->getRequest()->getParam('qty')) ?: 1;
        $quoteId = $helper->urlDecode(Mage::app()->getRequest()->getParam('quoteId'));
        $lineNumber = $helper->urlDecode(Mage::app()->getRequest()->getParam('lineNumber'));

        try {
            $product = Mage::getModel('catalog/product');
            /* @var $product Epicor_Comm_Model_Product */
            $errors = array();

            $product->setStoreId(Mage::app()->getStore()->getId())
                    ->load($product->getIdBySku($productSku));

            $prodArray = array();

            $cdm = Mage::getModel('epicor_comm/message_request_cdm');
            /* @var $cdm Epicor_Comm_Model_Message_Request_Cdm */

            $cdm->setProductSku($product->getSku());
            $cdm->setProductUom($product->getUom());
            $cdm->setGroupSequence($groupSequence);
            $cdm->setTimeStamp(null);
            $cdm->setQty($qty);
            $cdm->setEwaCode($ewaCode);
            $cdm->setQuoteId(!empty($quoteId) ? $quoteId : null);
            $cdm->setLineNumber($lineNumber);

            if ($cdm->sendMessage()) {

                $configurator = $cdm->getResponse()->getConfigurator();

                $ewaTitle = $configurator->getTitle();
                $ewaShortDescription = $configurator->getShortDescription();
                $ewaDescription = $configurator->getDescription();
                $ewaSku = $configurator->getConfiguredProductCode();
                $productCurrency = $configurator->getCurrencies()->getCurrency();

                $ewaAttributes = array(
                    array('description' => 'Ewa Code', 'value' => $ewaCode),
                    array('description' => 'Ewa Description', 'value' => $ewaDescription),
                    array('description' => 'Ewa Short Description', 'value' => $ewaShortDescription),
                    array('description' => 'Ewa SKU', 'value' => $ewaSku),
                    array('description' => 'Ewa Title', 'value' => $ewaTitle),
                );

                $product->setEwaCode($ewaCode);
                $product->setEwaSku($ewaSku);

                $product->setEwaDescription(base64_encode($ewaDescription));
                $product->setEwaShortDescription(base64_encode($ewaShortDescription));
                $product->setEwaTitle(base64_encode($ewaTitle));

                $product->setEwaAttributes(base64_encode(serialize($ewaAttributes)));

                $basePrice = $productCurrency->getBasePrice();
                $customerPrice = $productCurrency->getCustomerPrice();
                $product->setEccMsqBasePrice($basePrice);
                $product->unsFinalPrice();

                $customerPriceUsed = Mage::getStoreConfig('epicor_comm_enabled_messages/msq_request/cusomterpriceused');
                // Set prices
                if ($customerPriceUsed || $product->getTypeId() == 'bundle') {
                    // NOTe Bundle products cannot have special prices like other products 
                    // as it's expecting a percentage, not a price!
                    $product->setPrice($customerPrice);
                } else {
                    $product->setPrice($basePrice);
                    $product->setSpecialPrice($customerPrice);
                }

                $product->setFinalPrice($customerPrice);
                $product->setMinimalPrice($customerPrice);
                $product->setMinPrice($customerPrice);
                $product->setCustomerPrice($customerPrice);
                $product->setMsqQty($configurator->getQuantity());
                $product->setQty($configurator->getQuantity());
                $product->setUsePrice($customerPrice);



                $set = false;
                if (!Mage::registry('msq-processing')) {
                    Mage::register('msq-processing', true);
                    $set = true;
                }
                $product->getFinalPrice();
                if ($set) {
                    Mage::unregister('msq-processing');
                }

                /* @var $product Epicor_Comm_Model_Product */
                $price = $product->unsFinalPrice()->getFinalPrice(1);

                $mHelper = Mage::helper('epicor_comm/messaging');
                /* @var $mHelper Epicor_Comm_Helper_Messaging */
                $currencyCode = $mHelper->getCurrencyMapping($productCurrency->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);

                $formattedPrice = $helper->formatPrice($price, true, $currencyCode);
                $formattedTotal = $helper->formatPrice($price * 1, true, $currencyCode);
                $product->setMsqFormattedPrice($formattedPrice);
                $product->setMsqFormattedTotal($formattedTotal);

                $prodArray[] = $productHelper->getProductInfoArray($product);
            } else {
                $errors[] = $this->__('Failed to retrieve configured details.');
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Mage::getSingleton('core/session')->addError($error);
            }
        }

        $response = array(
            'errors' => $errors,
            'products' => $prodArray
        );
        Mage::register('line_add_json', str_replace('\\', '\\\\', json_encode($response)));
        Mage::register('double_parent', true);


        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('customerconnect/customer_rfqs_details_lineaddbyjs')->toHtml()
        );
    }

    public function configureproductAction()
    {
        $productId = Mage::app()->getRequest()->getParam('productid');
        $child = Mage::app()->getRequest()->getParam('child');
        $options = Mage::app()->getRequest()->getParam('options');

        if ($child) {
            $childProd = Mage::getModel('catalog/product')->load($child);
            Mage::register('child_product', $childProd);
        }

        if ($options) {
            $options = unserialize(base64_decode($options));
            $optionsData = array();
            foreach ($options as $option) {
                $optionsData[$option['description']] = $option['value'];
            }
            Mage::register('options_data', $optionsData);
        }

        $product = Mage::helper('catalog/product')->initProduct($productId, $this);
        /* @var $product Mage_Core_Model_Abstract */

        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        $block = $this->getLayout()->createBlock('epicor_comm/catalog_product_configure');
        /* @var $block Epicor_Comm_Block_Catalog_Product_Configure */

        $response = array(
            'error' => '',
            'html' => $block->toHtml(),
            'jsonconfig' => Mage::helper('core')->jsonDecode($block->getJsonConfig())
        );

        $this->getResponse()->setBody(json_encode($response));
    }

    public function submitconfigurationAction()
    {
        $productHelper = Mage::helper('epicor_comm/product');
        /* @var $productHelper Epicor_Comm_Helper_Product */

        $productId = Mage::app()->getRequest()->getParam('productid');
        $att = Mage::app()->getRequest()->getParam('super_attribute');
        $grp = Mage::app()->getRequest()->getParam('super_group');
        $opt = Mage::app()->getRequest()->getParam('options');
        $qty = Mage::app()->getRequest()->getParam('qty');
        $currencyCode = $this->getRequest()->getParam('currency_code');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $product = Mage::getModel('catalog/product');
        /* @var $product Epicor_Comm_Model_Product */

        $product->load($productId);

        $request = new Varien_Object(array(
            'product' => $productId,
            'super_group' => $grp,
            'super_attribute' => $att,
            'qty' => $qty,
            'options' => $opt
        ));

        $cartCandidates = $product->getTypeInstance(true)
                ->prepareForCartAdvanced($request, $product, null);

        $finalProduct = array();

        foreach ($cartCandidates as $candidate) {
            if ($product->getTypeId() == 'configurable') {
                if (@$candidate->getSku() != @$product->getSku()) {
                    $finalProduct = $candidate;
                }
            } else if ($product->getTypeId() == 'grouped') {
                $finalProduct[] = $candidate;
            } else {
                $finalProduct = $candidate;
            }
        }

        if (!$finalProduct) {
            $response = json_encode(array('error' => $cartCandidates));
        } else {
            /* @var $finalProduct Epicor_Comm_Model_Product */
            $msq = Mage::getModel('epicor_comm/message_request_msq');
            /* @var $msq Epicor_Comm_Model_Message_Request_Msq */
            $msq->setTrigger('RFQ configure');

            if (!empty($currencyCode)) {
                $currencyCode = $helper->getCurrencyMapping($currencyCode, Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);
                $msq->addCurrency($currencyCode);
            }

            if (!is_array($finalProduct)) {
                $msq->addProduct($finalProduct, $qty);
            } else {
                foreach ($finalProduct as $fProduct) {
                    $msq->addProduct($fProduct, $grp[$fProduct->getId()]);
                }
            }

            $success = $msq->sendMessage();
            $prodArray = array();

            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */

            $customer = $customerSession->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            if (!is_array($finalProduct)) {
                $this->_priceProduct($finalProduct, $qty, $currencyCode, $success);
                $product = Mage::getModel('catalog/product')->load($finalProduct->getId());
                $finalProduct->setUom($product->getUom());
                $prodArray[$productId] = $productHelper->getProductInfoArray($finalProduct);
            } else {
                $prodArray['grouped'] = array();
                foreach ($finalProduct as $fProduct) {
                    $this->_priceProduct($fProduct, $grp[$fProduct->getId()], $currencyCode, $success);
                    $product = Mage::getModel('catalog/product')->load($fProduct->getId());
                    $fProduct->setUom($product->getUom());

                    $prodArray['grouped'][] = $productHelper->getProductInfoArray($fProduct);
                }
            }

            $response = json_encode($prodArray);
        }
        $this->getResponse()->setBody($response);
    }

    private function _priceProduct(&$finalProduct, $qty, $currencyCode, $success)
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $price = $finalProduct->getPrice();
        $tierPrice = $finalProduct->getTierPrice($qty);

        if (is_array($tierPrice)) {
            $tierPrice = $tierPrice[0]['website_price'];
        }

        if (!is_null($tierPrice)) {
            $price = $price > 0 ? min($price, $tierPrice) : $tierPrice;
        }

        $special = $finalProduct->getSpecialPrice();

        if (!is_null($special)) {
            $price = $price > 0 ? min($price, $special) : $special;
        }

        $finalProduct->setUsePrice($price);

        $formattedPrice = $helper->formatPrice($price, true, $currencyCode);
        $formattedTotal = $helper->formatPrice($price * $qty, true, $currencyCode);
        $finalProduct->setMsqFormattedPrice($formattedPrice);
        $finalProduct->setMsqFormattedTotal($formattedTotal);
        $finalProduct->setMsqQty($qty);
        $finalProduct->setQty($qty);

        $optionValues = array();

        $customOptions = $finalProduct->getCustomOptions();
        $options = $finalProduct->getOptions();
        foreach ($options as $option) {
            /* @var $option Epicor_Comm_Model_Catalog_Product_Option */
            if (isset($customOptions['option_' . $option->getId()])) {
                $optionVal = $customOptions['option_' . $option->getId()];
                /* @var $optionVal Mage_Catalog_Model_Product_Configuration_Item_Option */
                $optionValues[] = array(
                    'description' => $option->getTitle(),
                    'value' => $optionVal->getValue()
                );
            }
        }

        $finalProduct->setOptionValues($optionValues);

        if (!empty($optionValues)) {
            $optionValues = base64_encode(serialize($optionValues));
        } else {
            $optionValues = '';
        }

        $finalProduct->setConfiguredOptions($optionValues);

        if ((!$success || !$finalProduct->getIsSalable())) {
            $finalProduct->setError(1);
        } else {
            $finalProduct->setError(0);
        }

        return $finalProduct;
    }

    protected function _initNewRfq()
    {
        $rfq = new Epicor_Common_Model_Xmlvarien();

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        // only add contact if you have a contact code
        if ($customer->getContactCode()) {

            $contact = new Epicor_Common_Model_Xmlvarien(
                    array(
                'number' => $customer->getContactCode(),
                'name' => $customer->getName(),
                    )
            );

            $contactArr = new Epicor_Common_Model_Xmlvarien(
                    array(
                'contact' => array(
                    $contact
                )
                    )
            );

            $rfq->setContacts($contactArr);
        }


        Mage::register('rfqs_editable', true);
        Mage::register('rfq_new', true);

        return $rfq;
    }

}
