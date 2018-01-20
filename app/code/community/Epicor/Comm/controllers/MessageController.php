<?php

/**
 * Data controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_MessageController extends Mage_Core_Controller_Front_Action
{

    /**
     * Offline Orders Test/Trigger Action
     */
    public function msqAction()
    {
        $msq = Mage::getModel('epicor_comm/message_request_msq');
        /* @var $msq Epicor_Comm_Model_Message_Request_Msq */
        $msq->setTrigger('API call');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $hasErrors = false;
        $productHelper = Mage::helper('epicor_comm/product');
        /* @var $productHelper Epicor_Comm_Helper_Product */

        $currencyCode = $this->getRequest()->getParam('currency_code');
        $skuParam = (array) $this->getRequest()->getParam('sku');
        $idParam = (array) $this->getRequest()->getParam('id');
        $from = $this->getRequest()->getParam('from');
        $erpAccountId = $this->getRequest()->getParam('erp_account_id');
        $saveValues = $this->getRequest()->getParam('save_values');
        $dontProcess = $this->getRequest()->getParam('dont_process');
        $qty = $this->getRequest()->getParam('qty');
        $ewaCode = $this->getRequest()->getParam('ewa');
        $attributes = $this->getRequest()->getParam('att');
        $useIndex = $this->getRequest()->getParam('use_index');

        $skuList = !empty($skuParam) ? $skuParam : $idParam;
        $productsCount = max(count($skuParam), count($idParam));

        $productsArray = array();
        for ($i = 0; $i < $productsCount; $i++) {
            if (isset($skuParam[$i])) {
                $productsArray[$i]['sku'] = $skuParam[$i];
            }
            if (isset($idParam[$i])) {
                $productsArray[$i]['id'] = $idParam[$i];
            }
        }

        $productsToSend = array();
        $qtys = array();
        $productsNotSent = array();
        $products = array();

        if (!empty($currencyCode)) {
            $msq->addCurrency($currencyCode);
        }

        if (!empty($currencyCode)) {
            $msq->setCustomerGroupId($erpAccountId);
        }

        if ($saveValues) {
            $msq->setSaveProductDetails(true);
        }

        $currencyCode = $helper->getCurrencyMapping($currencyCode, Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);

        foreach ($productsArray as $index => $p) {
            $uomSeparator = $helper->getUOMSeparator();
            if (isset($p['sku']) && strpos($p['sku'], $uomSeparator) !== false) {
                $product = $helper->findProductBySku($helper->getSku($p['sku']), $helper->getUom($p['sku']), false);
            } else if (isset($p['id']) && !empty($p['id'])) {
                $product = Mage::getModel('catalog/product')->load($p['id']);
                if ($product->getTypeId() == 'grouped') {
                    $product = $helper->findProductBySku($p['sku'], '', false);
                }
            } else if (isset($p['sku'])) {
                $product = $helper->findProductBySku($p['sku'], '', false);
            } else {
                continue;
            }

            /* @var $product Epicor_Comm_Model_Product */

            if (!$product && isset($p['sku'])) {
                $product = Mage::getModel('catalog/product');
                $product->setSku($p['sku']);
            }

            $skuQty = (is_array($qty)) ? $qty[$index] : 1;
            $skuEwaCode = (is_array($ewaCode)) ? $ewaCode[$index] : '';

            $att = array();
            if (!empty($skuEwaCode)) {
                $att['Ewa Code'] = $skuEwaCode;
            }

            $productAttributes = (is_array($attributes) && isset($attributes[$index]) ? (array) unserialize(base64_decode($attributes[$index])) : array());

            foreach ($productAttributes as $productAtt) {
                $productAttCode = @$productAtt['description'];
                $productAttValue = @$productAtt['value'];
                $att[$productAttCode] = $productAttValue;
            }
            $product->setMsqAttributes($att);
            $products[$index] = $product;

            $sendProduct = true;

            if ($from == 'rfq' && ($product->getTypeId() == 'configurable' || ($product->getTypeId() == 'grouped' && !$product->getStkType()))) {
                $sendProduct = false;
            }

            if ($product->getConfigurator() == 1 && (empty($skuEwaCode) && empty($att))) {
                $sendProduct = false;
            }

            if ($sendProduct) {
                //$productsToSend++;
                //$msq->addProduct($product, $skuQty);
                $productsToSend[$index] = $product;
                $qtys[$index] = $skuQty;
            } else {
                $productsNotSent[] = $index;
            }
        }
        
        $transportObject = new Varien_Object();
        $transportObject->setProducts($productsToSend);
        $transportObject->setMessage($msq);
        Mage::dispatchEvent('msq_sendrequest_before', array(
            'data_object' => $transportObject,
            'message' => $msq,
        ));
        $productsToSend = $transportObject->getProducts();
        foreach ($productsToSend as $index => $product) {
            $msq->addProduct($product, $qtys[$index]);
        }
        
        $msq->setPreventRounding(true);

        $success = (count($productsToSend) > 0) ? $msq->sendMessage() : true;
        
        Mage::dispatchEvent('msq_sendrequest_after', array(
            'data_object' => $transportObject,
            'message' => $msq,
        ));

        if (!empty($dontProcess)) {
            $this->getResponse()->setBody('');
            return;
        }

        $prodArray = array();

        foreach ($skuList as $index => $key) {
            $product = $products[$index];
            $skuQty = (is_array($qty)) ? $qty[$index] : 1;

            /* @var $product Epicor_Comm_Model_Product */

            $price = $productHelper->getProductPrice($product, $skuQty);
            $formattedPrice = $helper->formatPrice($price, true, $currencyCode);
            $formattedTotal = $helper->formatPrice($price * $skuQty, true, $currencyCode);

            $product->setUsePrice($price);
            $product->setMsqFormattedPrice($formattedPrice);
            $product->setMsqFormattedTotal($formattedTotal);
            $product->setMsqQty($skuQty);
            $product_status = $product->getStatus();
            $infoArray = $productHelper->getProductInfoArray($product);
            $infoArray['qty'] = $skuQty;
            $infoArray['sendSku'] = $key;

            $foundItem = $product->getId();
            if ((!$success || !$foundItem || !$product->getIsSalable()) && !in_array($index, $productsNotSent)) {
                $infoArray['error'] = 1;
                $hasErrors = true;
            }
            if ($product_status == 2) {
                $infoArray['status_error'] = 1;
                $hasErrors = true;
            }
           if(!in_array(Mage::app()->getWebsite()->getId(), $product->getWebsiteIds())){
                $infoArray['status_error'] = 1;
                $hasErrors = true;
            }


            $key = ($useIndex == 'row_id' ? $index : $key);
            $prodArray[$key] = $infoArray;
        }

        if ($hasErrors) {
            $prodArray['has_errors'] = 1;
        }


        $response = json_encode($prodArray);

        $this->getResponse()->setBody($response);
    }

    public function gorAction()
    {

        $orderID = $this->getRequest()->getParam('id');
        Mage::getModel('sales/order')->load($orderID);
        $order = Mage::getModel('sales/order')->load($orderID);

        if (!$order->isObjectNew()) {

            if (!Mage::registry("offline_order_{$order->getId()}")) {
                Mage::register("offline_order_{$order->getId()}", true);
            }

            Mage::dispatchEvent('sales_order_save_commit_after', array(
                'data_object' => $order,
                'order' => $order,
            ));

            Mage::unregister("offline_order_{$order->getId()}");
        }
    }

    public function csnsAction()
    {
        $serial = $this->getRequest()->getParam('serial');
        $sku = $this->getRequest()->getParam('sku');
        $productId = $this->getRequest()->getParam('product_id');
        $mode = $this->getRequest()->getParam('mode');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $found = false;

        if ($serial && $helper->isMessageEnabled('epicor_comm', 'csns')) {

            if (empty($sku) && $productId) {
                $product = Mage::getModel('catalog/product')->load($productId);
                /* @var $product Epicor_Comm_Model_Product */
                $sku = $helper->getSku($product->getSku());
            }

            $searches = array(
                'serial_number' => array(
                    'EQ' => $serial
                ),
                'product_code' => array(
                    'EQ' => $sku
                ),
            );

            $search = $helper->sendErpMessage('epicor_comm', 'csns', array(), $searches);

            if ($search['success']) {
                $message = $search['message'];

                $response = $message->getResponse();



                if ($response) {
                    $productsGroup = $response->getProducts();
                    $products = ($productsGroup) ? $productsGroup->getasarrayProduct() : array();
                    if (!empty($products)) {
                        $found = true;
                    }
                }
            }
        }

        if ($mode == 'validate') {
            $response = ($found) ? 'VALID' : 'INVALID';
        } else {
            // TBC: not actually needed at the mo, but may need an ajxable csns that returns results
            $response = '';
        }

        $this->getResponse()->setBody($response);
    }

    public function cimAction()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */
        $ewaCode = '';
        try {

            $productId = Mage::app()->getRequest()->getParam('productId');
            $quoteId = Mage::app()->getRequest()->getParam('quoteId');
            $action = Mage::app()->getRequest()->getParam('action');
            $lineNum = Mage::app()->getRequest()->getParam('lineNumber');

            $cimData = array(
                'ewa_code' => Mage::app()->getRequest()->getParam('ewaCode'),
                'group_sequence' => Mage::app()->getRequest()->getParam('groupSequence'),
                'quote_id' => !empty($quoteId) ? $quoteId : null,
                'action' => $action,
                'line_number' => $lineNum
            );

            $cim = $helper->sendCim($productId, $cimData);

            if ($cim->isSuccessfulStatusCode()) {
                $configurator = $cim->getResponse()->getConfigurator();
                $ewaCode = $configurator->getRelatedToRowId();
            } else {
                $error = $this->__('Failed to retrieve configured details.');
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $ewaAttributes = array(
            array('description' => 'Ewa Code', 'value' => $ewaCode),
        );

        $response = array(
            'error' => $error,
            'ewa_code' => $ewaCode,
            'ewa_attributes' => base64_encode(serialize($ewaAttributes))
        );

        $this->getResponse()->setBody(json_encode($response));
    }

    public function cdmAction()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */

        $ewaCode = Mage::app()->getRequest()->getParam('EWACode');
        $groupSequence = Mage::app()->getRequest()->getParam('groupSequence');
        $qty = Mage::app()->getRequest()->getParam('qty');
        $productSku = Mage::app()->getRequest()->getParam('SKU');
        $error = '';

        try {
            $product = Mage::getModel('catalog/product');
            /* @var $product Epicor_Comm_Model_Product */

            $product->setStoreId(Mage::app()->getStore()->getId())
                    ->load($product->getIdBySku($productSku));

            $prodArray = array();

            $cdm = Mage::getModel('epicor_comm/message_request_cdm');
            /* @var $cdm Epicor_Comm_Model_Message_Request_Cdm */

            $cdm->setProductSku($product->getSku());
            $cdm->setProductUom($product->getUom());
            $cdm->setTimeStamp(null);

            $cdm->setQty(1);

            if (!empty($ewaCode)) {
                $cdm->setEwaCode($ewaCode);
            }

            if (!empty($groupSequence)) {
                $cdm->setGroupSequence($groupSequence);
            }

            if ($cdm->sendMessage()) {
                $configurator = $cdm->getResponse()->getConfigurator();

                $prodArray = array(
                    'name' => $product->getName(),
                    'ewa_code' => $configurator->getEwaCode(),
                    'ewa_description' => $configurator->getShortDescription(),
                    'ewa_short_description' => $configurator->getShortDescription(),
                    'ewa_sku' => $configurator->getConfiguredProductCode(),
                    'ewa_title' => $configurator->getTitle()
                );
            } else {
                $error = $this->__('Failed to retrieve configured details.');
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $response = array(
            'error' => $error,
            'product' => $prodArray
        );

        $this->getResponse()->setBody(json_encode($response));
    }

    public function crquAction()
    {
        $helper = Mage::helper('customerconnect/rfq');
        /* @var $helper Epicor_Customerconnect_Helper_Rfq */
        $fileHelper = Mage::helper('epicor_common/file');
        /* @var $fileHelper Epicor_Common_Helper_File */
        $data = $this->getRequest()->getParam('data');

        if ($data) {
            $newData = unserialize(base64_decode($data));
            $crqu = Mage::getSingleton('customerconnect/message_request_crqu');
            /* @var $crqu Epicor_Customerconnect_Model_Message_Request_Crqu */
            $duplicate = isset($newData['is_duplicate']) ? true : false;
            if ($crqu->isActive() && $helper->getMessageType('CRQU')) {

                $aFiles = array();
                $lFiles = array();
                if (isset($newData['attachments'])) {
                    $aFiles = $fileHelper->processPageFiles('attachments', $newData, $duplicate, true);
                }

                if (isset($newData['lineattachments'])) {
                    $lFiles = $fileHelper->processPageFiles('lineattachments', $newData, $duplicate, true);
                }

                $files = array_merge($aFiles, $lFiles);

                $crqu->setAction('A');
                $crqu->setQuoteNumber('');
                $crqu->setQuoteSequence('');
                $crqu->setOldData(array());
                $crqu->setNewData($newData);
                $crqu->setAccountNumber($newData['account_number']);

                if ($crqu->sendMessage()) {
                    $rfq = $crqu->getResults();
                    $helper->processCrquFilesSuccess($files, $rfq);
                } else {
                    $helper->processCrquFilesFail($files);
                }
            }
        }
    }

}
