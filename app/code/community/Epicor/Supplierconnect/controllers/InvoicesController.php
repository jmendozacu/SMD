<?php

/**
 * Invoices controller
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */

class Epicor_Supplierconnect_InvoicesController extends Epicor_Supplierconnect_Controller_Abstract {

    /**
     * Index action 
     */
    public function indexAction() {
        $message = Mage::getSingleton('supplierconnect/message_request_suis');
        $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SUIS');       
   
        if($message->isActive() && $messageTypeCheck){ 
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('Invoice Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('customer/account/index');
            }
        }
    }

    public function detailsAction() {
        $helper = Mage::helper('supplierconnect');
        /* @var $helper Epicor_Supplierconnect_Helper_Data */
        $invoice_requested = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('invoice'))));

        $erp_account_number = Mage::helper('epicor_comm')->getSupplierAccountNumber();
        
        if (
                count($invoice_requested) == 2 &&
                $invoice_requested[0] == $erp_account_number &&
                !empty($invoice_requested[1])
        ) {
            $message = Mage::getSingleton('supplierconnect/message_request_suid');
            $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SUID');       
   
            if($message->isActive() && $messageTypeCheck){ 

                $message
                        ->setInvoiceNumber($invoice_requested[1])
                        ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                if ($message->sendMessage()) {
                    Mage::register('supplier_connect_invoice_details', $message->getResults());
                    $this->loadLayout()->renderLayout();
                } else {
                    Mage::getSingleton('core/session')->addError('Failed to retrieve Invoice Details');
                }
            } else {
                Mage::getSingleton('core/session')->addError('Invoice Details not available');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Invalid Invoice Number');
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            $this->_redirect('*/*/index');
        }
    }

}

