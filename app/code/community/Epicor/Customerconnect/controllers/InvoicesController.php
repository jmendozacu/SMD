<?php

/**
 * Invoices controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_InvoicesController
        extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $cuis = Mage::getSingleton("customerconnect/message_request_cuis");
        $messageTypeCheck = $cuis->getHelper("customerconnect/messaging")->getMessageType('CUIS');

        if ($cuis->isActive() && $messageTypeCheck) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($this->__("ERROR - Invoices Search not available"));
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    public function detailsAction()
    {
        if ($this->_loadInvoice()) {
            $this->loadLayout()->renderLayout();
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }

    public function reorderAction()
    {
        $invoice = false;
        if ($this->_loadInvoice()) {
            $invoice = Mage::registry('customer_connect_invoices_details');
            if (!empty($invoice)) {
                $lines = $invoice->getLines();
                if (is_object($lines)) {
                    $lineData = $lines->getasarrayLine();

                    foreach ($lineData as $x => $line) {
                        $line->setQuantity($line->getQuantities());
                        $lineData[$x] = $line;
                    }

                    $lines->setLine($lineData);
                    $invoice->setLines($lines);
                }
            }
        }
        
        $helper = Mage::helper('epicor_common/cart');
        /* @var $helper Epicor_Common_Helper_Cart */

        if (empty($invoice) || !$helper->processReorder($invoice)) {
            if (!Mage::getSingleton('core/session')->getMessages()->getItems()) {
                Mage::getSingleton('core/session')->addError($this->__('Failed to build cart for Re-Order request'));
            }

            $this->_redirect('checkout/cart/');

            $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('return'));

            $this->_redirectUrl($location);
        } else {
            $this->_redirect('checkout/cart/');
        }
    }

    /**
     * Export Invoices grid to CSV format
     */
    public function exportInvoicesCsvAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_invoices.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_invoices_list_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invoices grid to XML format
     */
    public function exportInvoicesXmlAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_invoices.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_invoices_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    private function _loadInvoice()
    {
        $loaded = false;
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erpAccountNumber = $helper->getErpAccountNumber();
        $invoice = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('invoice'))));

        if (
                count($invoice) == 2 &&
                $invoice[0] == $erpAccountNumber &&
                !empty($invoice[1])
        ) {
            $cuid = Mage::getSingleton("customerconnect/message_request_cuid");
            $messageTypeCheck = $cuid->getHelper("customerconnect/messaging")->getMessageType('CUID');

            if ($cuid->isActive() && $messageTypeCheck) {

                $cuid->setAccountNumber($erpAccountNumber)
                        ->setInvoiceNumber($invoice[1])
                        ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()))
                        ->setType($this->getRequest()->getParam('attribute_type'));

                if ($cuid->sendMessage()) {
                    Mage::register('customer_connect_invoices_details', $cuid->getResults());
                    $loaded = true;
                } else {
                    Mage::getSingleton('core/session')->addError($this->__("Failed to retrieve Invoice Details"));
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__("ERROR - Invoice Details not available"));
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__("ERROR - Invalid Invoice Number"));
        }

        return $loaded;
    }

}
