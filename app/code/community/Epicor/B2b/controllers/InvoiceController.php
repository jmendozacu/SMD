<?php

/**
 * Invoice controller
 *
 * @category   Epicor
 * @package    Epicor_B2b
 * @author     Epicor Websales Team
 */
class Epicor_B2b_InvoiceController extends Mage_Core_Controller_Front_Action {

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

        $this->getLayout()->getBlock('head')->setTitle($this->__('My B2B Invoices'));

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('b2b/invoice/');
        }

        $block = $this->getLayout()->getBlock('invoices');

        $invoices = $block->getInvoices();
        if (!is_object($invoices)) {

            Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to retrieve your invoices. Please try again later.'));
            $this->_redirect('customer/account');
            return false;
        }

        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->renderLayout();
    }

    /**
     * Invoice view page
     */
    public function viewAction() {
        $this->_viewAction();
    }

    /**
     * Order copy of invoice
     */
    public function copyAction() {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if (!$invoiceId) {
            $this->_forward('noRoute');
            return false;
        }
        $accountNumber = Mage::helper('epicor_comm/Messaging')->getErpAccountNumber();
        if (!$accountNumber) {
            Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to retrieve a valid account number.'));
        } else {
            $result = false;
            // Either send e-mail to customer support requesting an invoice copy to be sent out or send INV message
            if (Mage::getStoreConfigFlag('epicor_comm_enabled_messages/inv_request/inv_email')) {
                $result = $this->_sendInvEmail($accountNumber, $invoiceId);
            }
            if (Mage::getStoreConfigFlag('epicor_comm_enabled_messages/inv_request/inv_message')) {
                $result = $this->_sendInvMessage($accountNumber, $invoiceId);
            }

            if (!$result) {
                Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to request a copy of your invoice. Please try again later.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('A copy of your invoice has been requested'));
            }
        }
        $this->_redirect('b2b/invoice');
    }

    /**
     * Init layout, messages and set active block for invoice
     *
     * @return null
     */
    protected function _viewAction() {
        if (!$this->_loadValidInvoice()) {
            return false;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('b2b/invoice');
        }

        $block = $this->getLayout()->getBlock('invoice_view');

        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->renderLayout();
    }

    /**
     * Try to load valid invoice by invoice_id and register it
     *
     * @param int $invoiceId
     * @return bool
     */
    protected function _loadValidInvoice($invoiceId = null) {
        if (null === $invoiceId) {
            $invoiceId = $this->getRequest()->getParam('invoice_id');
        }
        if (!$invoiceId) {
            $this->_forward('noRoute');
            return false;
        }
        $invoice = $this->_getErpInvoice($invoiceId);
        if (!$invoice) {
            $this->_redirect('b2b/invoice');
            return false;
        }
        Mage::register('current_invoice', $invoice);
        return true;
    }

    private function _getErpInvoice($invoiceId) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $ivd = Mage::getModel('b2b/message_request_ivd');
        $ivd->setCustomer($customer);
        $ivd->setInvoiceNumber($invoiceId);
        if (!$ivd->sendMessage()) {
            Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to retrieve the invoice details. Please try again later.'));
            return false;
        } else {
            return $ivd->getErpInvoice();
        }
    }

    private function _sendInvEmail($accountNumber, $invoiceId) {

        $result = false;
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $email = Mage::getStoreConfig('epicor_comm_enabled_messages/inv_request/inv_email_address');
        if (!$email) {
            Mage::getSingleton('customer/session')->addError($this->__('Sorry, we were unable to process your request at this time.'));
        } else {
            $message = "A customer has requested a copy of an invoice: \n";
            $message .= "Magento Customer ID: " . $customer->getId() . "\n";
            $message .= "ERP Customer accountNumber: " . $accountNumber . "\n";
            //$message .= "Customer Name: " . $customer->getName() . "<br />";
            //$message .= "Customer Email Address: " . $customer->getEmail() . "<br />";
            $message .= "Invoice ID Requested: " . $invoiceId . "\n";

            $data = array('name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'telephone' => $customer->getTelephone(),
                'comment' => $message,
            );

            if (Mage::helper('epicor_comm/data')->sendEmail($data, $email)) {
                $result = true;
            }
        }
        return $result;
    }

    private function _sendInvMessage($accountNumber, $invoiceId) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $inv = Mage::getModel('b2b/message_request_inv');
        $inv->setCustomer($customer);
        $inv->setInvoiceNumber($invoiceId);
        return ($inv->sendMessage());
    }

}
