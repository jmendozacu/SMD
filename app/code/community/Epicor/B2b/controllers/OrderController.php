<?php

/**
 * Order controller
 *
 * @category   Epicor
 * @package    Epicor_B2b
 * @author     Epicor Websales Team
 */
class Epicor_B2b_OrderController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My B2B Orders'));

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('b2b/order/');
        }

        $block = $this->getLayout()->getBlock('orders');

        $orders = $block->getOrders();
        if (!is_object($orders)) {

            Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to retrieve your orders. Please try again later.'));
            $this->_redirect('customer/account');
            return false;
        }

        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->renderLayout();
    }

    /**
     * Order view page
     */
    public function editAction() {
        Mage::register('edit_order', true);
        $this->_viewAction();
    }

    /**
     * Order view page
     */
    public function viewAction() {
        $this->_viewAction();
    }

    public function updateAction() {
        $orderId = $this->getOrderId();
        $numLines = (int) $this->getRequest()->getParam('lines');

        $data = $this->getRequest()->getPost();
        if ($data && $orderId!==false) {
            $som = Mage::getModel('epicor_comm/message_request_som');
            $som->setOrderId($orderId);
            $som->setProductArray($data['product']);
            $som->setOrderReference($data['reference']);
            if ($som->sendMessage()) {
                Mage::getSingleton('customer/session')->addSuccess($this->__($som->getStatusDescription()));
            } else {
                Mage::getSingleton('customer/session')->addError($this->__('Error in modifing order'));
                Mage::getSingleton('customer/session')->addError($this->__($som->getStatusDescription()));
            }
        }
        $encodedOrderId = Mage::helper('epicor_comm/Messaging')->encryptCustomerText($orderId);
        $this->_redirect('*/*/edit', array('order_id' => $encodedOrderId, 'lines' => $numLines));
    }

    /**
     * Init layout, messages and set active block for customer
     *
     * @return null
     */
    protected function _viewAction($handle = null) {
        if (!$this->_loadValidOrder()) {
            return false;
        }


        if ($handle)
            $this->loadLayout()->loadLayout($handle);
        else
            $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('b2b/order');
        }

        $block = $this->getLayout()->getBlock('order_view');

        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->renderLayout();
    }

    private function getOrderId() {
        $encoded = $this->getRequest()->getParam('order_id');
        return Mage::helper('epicor_comm/Messaging')->decrypt($encoded);
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null) {
        $result = true;
        if (null === $orderId) {
            $orderId = $this->getOrderId();
        }


        //  if (null === $numLines) {
        $numLines = (int) $this->getRequest()->getParam('lines');
        // }
        if (!$orderId) {
            Mage::getSingleton('customer/session')->addError($this->__('The order you selected could not be retrieved.'));
            $this->_redirect('b2b/order');
            $result = false;
        } else {
            $order = $this->_getErpOrder($orderId, $numLines);
            if (!$order) {
                Mage::getSingleton('customer/session')->addError($this->__('The order you selected could not be retrieved.'));
                $this->_redirect('b2b/order');
                $result = false;
            } else {
                Mage::register('current_order', $order);
            }
        }
        return $result;
    }

    private function _getErpOrder($orderId, $numLines) {
        $accountNumber = Mage::helper('epicor_comm/Messaging')->getErpAccountNumber();
        $result = false;
        if (!$accountNumber) {
            Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to retrieve a valid account number.'));
        } else {
            $sod = Mage::getModel('epicor_comm/message_request_sod');
            $sod->setErpOrderNumber($orderId);
            $sod->setNumberOfLines($numLines);
            if ($sod->sendMessage()) {
                $result = $sod->getErpOrder();
            }
        }
        return $result;
    }

}
