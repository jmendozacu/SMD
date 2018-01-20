<?php

/**
 * Orders controller
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_OrdersController extends Epicor_Supplierconnect_Controller_Abstract {

    /**
     * Index action - purchase order list page
     */
    public function indexAction() {
        $message = Mage::getSingleton('supplierconnect/message_request_spos');
        $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPOS');

        if ($message->isActive() && $messageTypeCheck) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('Order Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('customer/account/index');
            }
        }
    }

    /**
     * New purchase order list page
     */
    public function newAction() {
        $message = Mage::getSingleton('supplierconnect/message_request_spos');

        $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPOS');

        if ($message->isActive() && $messageTypeCheck) {
            $accessHelper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */
            Mage::register('orders_editable',$accessHelper->customerHasAccess('Epicor_Supplierconnect','Orders','confirmnew','','Access'));
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('Order Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('customer/account/index');
            }
        }
    }

    /**
     * Confirm / reject new submit action
     */
    public function confirmnewAction() {
        $data = $this->getRequest()->getPost();

        if ($data) {
            
            $message = Mage::getSingleton('supplierconnect/message_request_spoc');
            /* @var $message Epicor_Supplierconnect_Model_Message_Request_Spoc */

            $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPOC');

            if ($message->isActive() && $messageTypeCheck) {

                if (empty($data['confirmed']) && empty($data['rejected'])) {
                    Mage::getSingleton('core/session')->addError('No POs selected');
                } else {
                    $message->setPurchaseOrderData($data['purchase_order']);

                    if (isset($data['confirmed']) && !empty($data['confirmed'])) {
                        $message->setConfirmed($data['confirmed']);
                    }

                    if (isset($data['rejected']) && !empty($data['rejected'])) {
                        $message->setRejected($data['rejected']);
                    }

                    if ($message->sendMessage()) {
                        Mage::getSingleton('core/session')->addSuccess('Purchase Orders processed successfully');
                    } else {
                        Mage::getSingleton('core/session')->addError('Failed to process Purchase Orders ');
                    }
                }
            } else {
                Mage::getSingleton('core/session')->addError('Purchase Order updating not available');
            }

            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('*/*/new');
            }
        }
    }

    /**
     * Changed purchase order list page
     */
    public function changesAction() {
        $message = Mage::getSingleton('supplierconnect/message_request_spcs');

        $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPCS');

        if ($message->isActive() && $messageTypeCheck) {
            $accessHelper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */
            Mage::register('orders_editable',$accessHelper->customerHasAccess('Epicor_Supplierconnect','Orders','confirmchanges','','Access'));
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('Order Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('customer/account/index');
            }
        }
    }

    /**
     * Confirm / reject changes submit action
     */
    public function confirmchangesAction() {
        $data = $this->getRequest()->getPost();

        if ($data) {
            
            $message = Mage::getSingleton('supplierconnect/message_request_spcc');
            /* @var $message Epicor_Supplierconnect_Model_Message_Request_Spcc */

            $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPCC');

            if ($message->isActive() && $messageTypeCheck) {

                if (!isset($data['actions']) || empty($data['actions'])) {
                    Mage::getSingleton('core/session')->addError('No PO Lines selected');
                } else {

                    $message->setActions($data['actions']);

                    if ($message->sendMessage()) {
                        Mage::getSingleton('core/session')->addSuccess('Purchase Orders processed successfully');
                    } else {
                        Mage::getSingleton('core/session')->addError('Failed to process Purchase Orders ');
                    }
                }
            } else {
                Mage::getSingleton('core/session')->addError('Purchase Order updating not available');
            }

            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('*/*/changes');
            }
        }
    }
    
    /**
     * Order details page
     */
    public function detailsAction() {
        $helper = Mage::helper('supplierconnect');
        $order_requested = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('order'))));

        $erp_account_number = Mage::helper('epicor_comm')->getSupplierAccountNumber();
        
        if (
                count($order_requested) == 2 &&
                $order_requested[0] == $erp_account_number &&
                !empty($order_requested[1])
        ) {
            $message = Mage::getSingleton('supplierconnect/message_request_spod');

            $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPOD');

            if ($message->isActive() && $messageTypeCheck) {

                $message
                        ->setPurchaseOrderNumber($order_requested[1])
                        ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                if ($message->sendMessage()) {

                    $purchaseOrder = $message->getResults();

                    Mage::register('supplier_connect_order_details', $purchaseOrder);
//                    Mage::getSingleton('core/session')->setLastPurchaseOrder($purchaseOrder);

                    if ($purchaseOrder->getPurchaseOrder()) {
                        if ($purchaseOrder->getPurchaseOrder()->getOrderConfirmed() != '') {
                            Mage::register('supplier_connect_order_display', 'edit');
                        } else {
                            Mage::register('supplier_connect_order_display', 'view');
                        }
                    }
                    
                    $accessHelper = Mage::helper('epicor_common/access');
                    /* @var $helper Epicor_Common_Helper_Access */
                    if(!$accessHelper->customerHasAccess('Epicor_Supplierconnect','Orders','update','','Access')) {
                        Mage::unregister('supplier_connect_order_display');
                        Mage::register('supplier_connect_order_display', 'view');
                    }

                    $this->loadLayout()->renderLayout();
                } else {
                    Mage::getSingleton('core/session')->addError('Failed to retrieve Order Details');
                }
            } else {
                Mage::getSingleton('core/session')->addError('Order Details not available');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Invalid Order Number');
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Order update submit action
     */
    public function updateAction() {
        $data = $this->getRequest()->getPost();

//        $msgData = Mage::getSingleton('core/session')->getLastPurchaseOrder();
        $msgData = unserialize(base64_decode($this->getRequest()->getParam('oldData')));
        
        if ($data && $msgData) {

            $helper = Mage::helper('supplierconnect');
            /* @var $helper Epicor_Supplierconnect_Helper_Data */
            $order_requested = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('order'))));

            $erp_account_number = Mage::helper('epicor_comm')->getSupplierAccountNumber();
            
            if (
                    count($order_requested) == 2 &&
                    $order_requested[0] == $erp_account_number &&
                    !empty($order_requested[1])
            ) {
                $message = Mage::getSingleton('supplierconnect/message_request_spou');
                /* @var $message Epicor_Supplierconnect_Model_Message_Request_Spou */

                $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPOU');

                if ($message->isActive() && $messageTypeCheck) {

                    $message->setPurchaseOrderNumber($order_requested[1])
                            ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                    $message->setOldPurchaseOrderData($msgData);
                    $message->setNewPurchaseOrderData($data['purchase_order']);

                    if ($message->sendMessage()) {
                        Mage::getSingleton('core/session')->addSuccess('Purchse Order Update Request Sent');
                    } else {
                        Mage::getSingleton('core/session')->addError('Failed to retrieve Order Details');
                    }
                } else {
                    Mage::getSingleton('core/session')->addError('Order Details not available');
                }
            } else {
                Mage::getSingleton('core/session')->addError('Invalid Order Number');
            }

            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                
                $params = array('order' => Mage::app()->getRequest()->getParam('order'));
                
                if(!empty($data['back'])) {
                    $params['back'] = $data['back'];
                }
                
                
                $this->_redirect('*/*/details', $params);
            }
        }
    }

}