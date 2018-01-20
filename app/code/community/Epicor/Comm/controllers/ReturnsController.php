<?php

/**
 * Returns controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_ReturnsController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if (!$helper->isReturnsEnabled()) {
            Mage::getSingleton('customer/session')->addError('Returns not available');
            $this->_redirect('/');
        }
    }

    public function indexAction()
    {
        $session = Mage::getSingleton('customer/session');
        /* @var $session Mage_Customer_Model_Session */

        $session->unsReturnGuestName();
        $session->unsReturnGuestEmail();

        $loadLayout = true;
        $returnId = Mage::app()->getRequest()->getParam('return');
        $erpReturn = Mage::app()->getRequest()->getParam('erpreturn');

        if (!empty($returnId)) {
            $return = $this->loadReturn($returnId, true);
            if (!$return) {
                Mage::getSingleton('core/session')->addError('Return not found');
                $this->_redirect('/');
                $loadLayout = false;
            }
        } else if (!empty($erpReturn)) {
            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */

            $return = $helper->loadErpReturn($helper->decodeReturn($erpReturn), null, true);

            if ($return) {
                $return = $this->loadReturn($return->getId(), false, $return);
            } else {
                Mage::getSingleton('core/session')->addError('Return not found');
                $this->_redirect('/');
                $loadLayout = false;
            }
        }

        if ($loadLayout) {
            $this->loadLayout()->renderLayout();
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('epicor_comm/returns/index'));
        }
    }

    public function viewAction()
    {
        $success = false;
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        Mage::register('review_display', true);
        Mage::register('details_display', true);

        $returnInfo = Mage::app()->getRequest()->getParam('return');
        $returnData = unserialize($helper->decrypt($helper->urlDecode($returnInfo)));
        $returnId = (isset($returnData['id'])) ? $returnData['id'] : '';

        $return = Mage::getModel('epicor_comm/customer_return')->load($returnId);
        /* @var $return Epicor_Comm_Model_Customer_Return */

        if (!$return->isObjectNew()) {
            if ($return->canBeAccessedByCustomer()) {
                if ($return->getErpReturnsNumber()) {
                    $return->updateFromErp();
                }

                Mage::register('return_model', $return);
                $success = true;
            } else {
                Mage::getSingleton('core/session')->addError('You do not have permission to access this return');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Failed to retrieve return details');
        }

        if ($success) {
            $this->loadLayout()->renderLayout();
        } else {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }

    public function guestLoginAction()
    {
        if ($this->getRequest()->isPost()) {
            $errors = array();
            $shiptoName = $this->getRequest()->getParam('shipto_name', false);
            $emailAddress = $this->getRequest()->getParam('email_address', false);

            if ($shiptoName === false) {
                $errors[] = $this->__('Ship To Name Empty');
            }

            if ($emailAddress === false) {
                $errors[] = $this->__('Email Address Empty');
            }

            if ($shiptoName !== false && $emailAddress !== false) {
                $session = Mage::getSingleton('customer/session');
                /* @var $session Mage_Customer_Model_Session */

                $customer = Mage::getModel('customer/customer');
                /* @var $customer Epicor_Comm_Model_Customer */

                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail($emailAddress);

                if ($customer->getId()) {
                    $errors[] = $this->__('You must log in to proceed');
                } else {
                    $session->setReturnGuestName($shiptoName);
                    $session->setReturnGuestEmail($emailAddress);

                    Mage::register('guest_name', $shiptoName);
                    Mage::register('guest_email', $emailAddress);
                }
            }

            $this->sendStepResponse('login', $errors);
        }
    }

    public function createReturnAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */

            $errors = array();
            $cusRef = $this->getRequest()->getParam('customer_ref', false);
            $caseNum = $this->getRequest()->getParam('case_number', false);

            if ($cusRef === false) {
                $errors[] = $this->__('Customer Reference Empty');
            } else {

                $return = $helper->findReturn('customer_ref', $cusRef);

                if ($return['found']) {
                    $errors[] = $this->__('A return already exists with the supplied customer reference');
                } else {

                    $return = Mage::getModel('epicor_comm/customer_return');
                    /* @var $return Epicor_Comm_Model_Customer_Return */

                    if (!empty($caseNum)) {
                        $caseInfo = $helper->findCase($caseNum);

                        if (!$caseInfo['valid']) {
                            $errors[] = $this->__('Not a valid Case Number');
                        } else if (!empty($caseInfo['erp_return_number'])) {
                            $errors[] = $this->__('A return already exists with the supplied Case Number');
                        } else {
                            $return->setRmaCaseNumber($caseNum);
                        }
                    }

                    $guestEmail = $this->getRequest()->getParam('guest_email', false);
                    $guestName = $this->getRequest()->getParam('guest_name', false);

                    if (!empty($guestName)) {
                        $guestName = $helper->decodeReturn($guestName);
                        $return->setCustomerName($guestName);
                        Mage::register('guest_name', $guestName);
                    }

                    if (!empty($guestEmail)) {
                        $guestEmail = $helper->decodeReturn($guestEmail);
                        $return->setEmailAddress($guestEmail);
                        Mage::register('guest_email', $guestEmail);
                    }

                    if (empty($guestName) && empty($guestEmail)) {
                        // set customer info here
                        $customer = Mage::getSingleton('customer/session')->getCustomer();
                        /* @var $customer Epicor_Comm_Model_Customer */

                        $commHelper = Mage::helper('epicor_comm');
                        /* @var $commHelper Epicor_Comm_Helper_Data */
                        $erpAccount = $commHelper->getErpAccountInfo();
                        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

                        $return->setErpAccountId($erpAccount->getId());
                        $return->setIsGlobal(0);
                        $return->setCustomerName($customer->getName());

                        $return->setRmaContact($customer->getContactCode());

                        $shipTo = $customer->getDefaultShippingAddress();
                        /* @var $shipTo Mage_Customer_Model_Address */

                        if ($shipTo) {
                            $return->setAddressCode($shipTo->getErpAddressCode());
                        }
                        $return->setCustomerId($customer->getId());
                        $return->setEmailAddress($customer->getEmail());
                    }

                    $return->setStoreId(Mage::app()->getStore()->getId());

                    $return->setActions('All');
                    $return->setRmaDate(now());

                    if (empty($errors)) {
                        $return->setCustomerReference($cusRef);
                        $return->save();
                    }

                    Mage::register('return_model', $return);
                    Mage::register('return_id', $return->getId());
                }
            }

            $this->sendStepResponse('return', $errors);
        }
    }

    public function findReturnAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            /* Do action stuff here */
            $errors = array();
            $findType = $this->getRequest()->getParam('find_type', false);
            $findValue = $this->getRequest()->getParam('find_value', false);

            if ($findType === false) {
                $errors[] = $this->__('Find Return by Type Missing');
            }

            if ($findValue === false) {
                $errors[] = $this->__('Find Value Missing');
            }

            if (empty($errors)) {
                $helper = Mage::helper('epicor_comm/returns');
                /* @var $helper Epicor_Comm_Helper_Returns */
                $return = $helper->findReturn($findType, $findValue, true);

                if (empty($return['errors'])) {
                    $returnObj = $return['return'];
                    /* @var $returnObj Epicor_Comm_Model_Customer_Return */
                    if ($return['source'] == 'local') {
                        $returnObj->updateFromErp();
                    }

                    $returnObj->reloadChildren();

                    Mage::register('return_id', $returnObj->getId());
                    Mage::register('return_model', $returnObj);
                } else {
                    $errors = $return['errors'];
                }
            }

            $this->sendStepResponse('return', $errors);
        }
    }

    public function updateReferenceAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            /* Do action stuff here */
            $errors = array();

            $return = $this->loadReturn();

            if (!$return->isObjectNew()) {
                $ref = $this->getRequest()->getParam('customer_ref', '');
                $caseNum = $this->getRequest()->getParam('case_number', '');

                if (!$return->getErpReturnsNumber() && $caseNum != $return->getRmaCaseNumber()) {
                    if (!empty($caseNum)) {
                        $helper = Mage::helper('epicor_comm/returns');
                        /* @var $helper Epicor_Comm_Helper_Returns */

                        $caseInfo = $helper->findCase($caseNum);

                        if (!$caseInfo['valid']) {
                            $errors[] = $this->__('Not a valid Case Number');
                        } else if (!empty($caseInfo['erp_return_number'])) {
                            $errors[] = $this->__('A return already exists with the supplied Case Number');
                        } else {
                            $return->setRmaCaseNumber($caseNum);
                        }
                    } else {
                        $return->setRmaCaseNumber($caseNum);
                    }
                }

                $return->setCustomerReference($ref);
                $return->save();
            } else {
                $errors[] = $this->__('Failed to find return to add reference to. Please try again.');
            }

            $this->sendStepResponse('return', $errors);
        }
    }

    public function addProductAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */
            /* Do action stuff here */
            $errors = array();

            $productId = $this->getRequest()->getParam('productid', false);
            $sku = $this->getRequest()->getParam('sku', false);
            $qty = $this->getRequest()->getParam('qty', false);
            $uom = $this->getRequest()->getParam('uom', false);

            $lines = array();

            if ($sku === false) {
                $errors[] = $this->__('SKU is Empty');
            }
            if ($qty === false) {
                $errors[] = $this->__('Qty is Empty');
            }

            if (empty($errors)) {

                $product = Mage::getModel('catalog/product');
                /* @var $product Epicor_Comm_Model_Product */

                if (!empty($productId)) {
                    $product = $product->load($productId);
                }

                if ($product->isObjectNew()) {
                    $product = $helper->findProductBySku($sku, $uom);
                }

                $allowed = true;

                if ($product && $product->getDataSource() == 'erp' && !$helper->configHasValue('allow_skus_type', 'erp')) {
                    $allowed = false;
                }

                if ($product && $allowed) {
                    $sku = $helper->stripProductCodeUOM($product->getSku());

                    $lines[] = array(
                        'sku' => $sku,
                        'qty_returned' => $qty,
                        'uom' => $product->getUom(),
                        'source' => 'SKU',
                        'source_label' => 'SKU',
                        'source_data' => '',
                        'type_id' => $product->getTypeId(),
                        'entity_id' => $product->getId(),
                    );
                } else if (!$product && $helper->configHasValue('allow_skus_type', 'custom')) {
                    $lines[] = array(
                        'sku' => $sku,
                        'qty_returned' => $qty,
                        'uom' => '',
                        'source' => 'SKU',
                        'source_label' => 'SKU',
                        'source_data' => '',
                        'type_id' => '',
                        'entity_id' => '',
                    );
                } else {
                    $errors[] = $this->__('Could not find product by SKU');
                }
            }

            if (empty($errors)) {
                $result = array('lines' => $lines);

                if (!$helper->checkConfigFlag('allow_mixed_return')) {
                    $result['hide_find_by'] = 1;
                }
            } else {
                if (!is_array($errors)) {
                    $errors = array($errors);
                }
                $result = array('errors' => $errors);
            }

            $this->getResponse()->setBody($helper->jsonEncode($result));
        }
    }

    public function findProductAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            /* Do action stuff here */
            $errors = array();

            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */

            $findType = $this->getRequest()->getParam('search_type', false);
            $findValue = $this->getRequest()->getParam('search_value', false);
            $lines = array();

            if (empty($findType)) {
                $errors[] = $this->__('Find Products Type Missing');
            }

            if (empty($findValue)) {
                $errors[] = $this->__('Find Products Value Missing');
            }

            $returnUserType = $helper->getReturnUserType();

            if (empty($errors)) {

                $lines = array();

                if ($findType == 'order') {
                    $order = $helper->findLocalOrder($findValue);

                    if ($order && !$order->isObjectNew()) {
                        $erpOrderNum = $order->getErpOrderNumber();
                        if (empty($erpOrderNum) && $returnUserType != 'b2b') {
                            $errors[] = $this->__('Not a Valid Order');
                        } else if (!empty($erpOrderNum)) {
                            $findValue = $erpOrderNum;
                        }
                    } else if ($returnUserType != 'b2b') {
                        $errors[] = $this->__('Not a Valid Order');
                    }
                }

                if (empty($lines) && empty($errors)) {
                    $products = $helper->findProductsByMessage($findType, $findValue);

                    if (empty($products['errors'])) {
                        $lines = $products['products'];
                    } else {
                        $errors = $products['errors'];
                    }
                }
            }

            if (empty($errors)) {
                $result = array('lines' => $lines);
                if (!$helper->checkConfigFlag('allow_mixed_return')) {
                    $result['hide_add_sku'] = 1;
                    $result['restrict_type'] = $findType;
                }
            } else {
                if (!is_array($errors)) {
                    $errors = array($errors);
                }
                $result = array('errors' => $errors);
            }

            $this->getResponse()->setBody($helper->jsonEncode($result));
        }
    }

    public function saveLinesAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */
            /* Do action stuff here */
            $errors = array();

            $return = $this->loadReturn();

            if ($return && !$return->isObjectNew()) {
                $linesPost = $this->getRequest()->getParam('lines', false);
                $attachmentsPost = $this->getRequest()->getParam('lineattachments', false);
                $lines = $helper->processPostedLines($return, $linesPost);
                if(array_key_exists('length_incorrect', $lines)){
                    $errors[] = $lines['length_incorrect'];
                }
                $helper->processPostedAttachments($return, $attachmentsPost, 'lineattachments', $lines);
            } else {
                $errors[] = $this->__('Failed to find return to add lines to. Please try again.');
            }
            
            $subStep = !empty($errors)? 'lines' : false;
            Mage::register('response_json', $this->sendStepResponse('products', $errors, true, $subStep));

            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/customer_returns_iframeresponse')->toHtml()
            );
        }
    }

    public function saveAttachmentsAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $helper = Mage::helper('epicor_comm/returns');
            /* @var $helper Epicor_Comm_Helper_Returns */
            /* Do action stuff here */
            $errors = array();
            $return = $this->loadReturn();

            if (!$return->isObjectNew()) {
                if ($return->isActionAllowed('Attachments')) {
                    $attachmentsPost = $this->getRequest()->getParam('attachments', false);
                    $helper->processPostedAttachments($return, $attachmentsPost, 'attachments');
                }
            } else {
                $errors[] = $this->__('Failed to find return to add attachments to. Please try again.');
            }

            Mage::register('response_json', $this->sendStepResponse('attachments', $errors, true));

            $this->getResponse()->setBody(
                    $this->getLayout()->createBlock('epicor_comm/customer_returns_iframeresponse')->toHtml()
            );
        }
    }

    public function saveNotesAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            /* Do action stuff here */
            $errors = array();

            $return = $this->loadReturn();

            if (!$return->isObjectNew()) {
                if ($return->isActionAllowed('Notes') && Mage::getStoreConfig('epicor_comm_returns/notes/tab_required')) {
                    $tabLength = Mage::getStoreConfig('epicor_comm_returns/notes/tab_length');
                    $note = $this->getRequest()->getParam('return-note', '');
                    if($tabLength && $tabLength < strlen($note)){
                        $errors[] = $this->__("The notes field exceeds the {$tabLength} characters allowed.");
                        $note = substr($note,0, $tabLength);
                    }                        
                    $return->setNoteText($note);
                    $return->save();
                   
                }
            } else {
                $errors[] = $this->__('Failed to find return to add notes to. Please try again.');
            }
            
            $this->getRequest()->getParam('return-note', false);

            $this->sendStepResponse('notes', $errors);
        }
    }

    public function saveReviewAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            /* Do action stuff here */
            $errors = array();

            $return = $this->loadReturn();

            if (!$return->isObjectNew()) {

                $return->setSubmitted(1);

                if ($return->getErpReturnsNumber()) {
                    $return->setErpSyncAction('U');
                } else {
                    $return->setErpSyncAction('A');
                }

                $return->setErpSyncStatus('N');

                if ($return->save()) {
                    Mage::register('return_success', true);
                } else {
                    $errors[] = $this->__('An error occurred saving your return, please try again later');
                }
            } else {
                $errors[] = $this->__('Failed to find return to confirm. Please try again.');
            }

            $this->sendStepResponse('review', $errors);
        }
    }

    public function deleteAction()
    {
        
    }

    /**
     * Loads the current return 
     * 
     * @return Epicor_Comm_Model_Customer_Return
     */
    private function loadReturn($returnId = null, $updateFromErp = false, $returnObj = null)
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if ($returnObj) {
            $return = $returnObj;
        } else {
            $returnId = $this->getRequest()->getParam('return_id', $returnId);
            $return = Mage::getModel('epicor_comm/customer_return');
            /* @var $return Epicor_Comm_Model_Customer_Return */

            if (!empty($returnId)) {
                $returnId = $helper->decodeReturn($returnId);
                $return->load($returnId);
            }
        }

        if (!$return->getId()) {
            Mage::getSingleton('core/session')->addError('Return not found');
            $return = false;
        } else {
            if ($return->canBeAccessedByCustomer()) {
                if ($updateFromErp) {
                    $return->updateFromErp();
                }
                Mage::register('return_id', $returnId);
                Mage::register('return_model', $return);
            } else {
                Mage::getSingleton('core/session')->addError('You do not have permission to access this return');
                $return = false;
            }
        }

        return $return;
    }

    protected function sendStepResponse($step, $errors = array(), $return = false, $substep = false)
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */
        if (empty($errors)) {
            $result = $helper->getNextReturnsStep($step);
        } else {
            if (!is_array($errors)) {
                $errors = array($errors);
            }
            $step = $substep ? $substep : null;
            //only add tab entry if substep is set (currently only for lines)
            $result = array('errors' => $errors);
            if($substep){                
                $result['tab'] = 'lines';
            }
        }

        if ($return) {
            return $helper->jsonEncode($result);
        } else {
            $this->getResponse()->setBody($helper->jsonEncode($result));
        }
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        $expired = false;

        $session = Mage::getSingleton('customer/session');
        /* @var $session Mage_Customer_Model_Session */

        if (!$session->isLoggedIn()) {
            $name = $session->getReturnGuestName();
            $email = $session->getReturnGuestEmail();

            if (empty($name) || empty($email)) {
                $expired = true;
            }
        }

        if ($expired) {
            $this->_ajaxRedirectResponse();
        }

        return $expired;
    }

    /**
     * Send Ajax redirect response
     *
     * @return Mage_Checkout_OnepageController
     */
    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Session Expired')
                ->setHeader('Login-Required', 'true')
                ->sendResponse();
        return $this;
    }

    public function listAction()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $this->loadLayout()->renderLayout();
    }

    public function createReturnFromDocumentAction()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if ($helper->isReturnsEnabled() && $helper->checkConfigFlag('allow_create')) {

            $type = $this->getRequest()->getParam('type', false);
            $data = $this->getRequest()->getParam('data', false);
            $returnUrl = $this->getRequest()->getParam('return', false);

            // create return object
            $return = $helper->createReturnFromDocument($type, $data);

            if ($return->getId()) {
                $this->_redirect('*/*/index', array('return' => $helper->encodeReturn($return->getId())));
            } else {
                $location = Mage::helper('core/url')->urlDecode(Mage::helper('core/url')->urlDecode($returnUrl));

                $this->_redirectUrl($location);
            }
        }
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
            foreach($options as $option) {
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
        $productId = Mage::app()->getRequest()->getParam('productid');
        $att = Mage::app()->getRequest()->getParam('super_attribute');
        $grp = Mage::app()->getRequest()->getParam('super_group');
        $opt = Mage::app()->getRequest()->getParam('options');
        $qty = Mage::app()->getRequest()->getParam('qty');
                
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
                if ($candidate->getSku() != $product->getSku()) {
                    $finalProduct = $candidate;
                }
            } else if ($product->getTypeId() == 'grouped') {
                $finalProduct[] = $candidate;
            } else {
                $finalProduct = $candidate;
            }
        }
        
        if(!$finalProduct) {
            $response = json_encode(array('error' => $cartCandidates));
        } else {
            /* @var $finalProduct Epicor_Comm_Model_Product */

            $prodArray = array();
            if (!is_array($finalProduct)) {
                $product = Mage::getModel('catalog/product')->load($finalProduct->getId());
                $product->setQty($qty);
                $prodArray[$productId] = $product->getData();
            } else {
                $prodArray['grouped'] = array();
                foreach ($finalProduct as $fProduct) {
                    $product = Mage::getModel('catalog/product')->load($fProduct->getId());
                    $product->setQty($grp[$fProduct->getId()]);
                    $prodArray['grouped'][] = $product->getData();
                }
            }

            $response = json_encode($prodArray);
        }
        $this->getResponse()->setBody($response);
    }
}
