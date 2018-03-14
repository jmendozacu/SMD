<?php

/**
 * AR Payments controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_ArpaymentsController extends Epicor_Customerconnect_Controller_Abstract
{
    
    /**
     * Index action 
     */
    public function indexAction()
    {
        $checkCapsActive  = Mage::getModel('customerconnect/arpayments')->checkArpaymentsActive();
        $caps             = Mage::getSingleton("customerconnect/message_request_caps");
        $postparams       = Mage::App()->getRequest()->getParam('prevStep');
        $messageTypeCheck = $caps->getHelper("customerconnect/messaging")->getMessageType('CAPS');
        $prevStep         = $postparams;
        if ($caps->isActive() && $messageTypeCheck && $prevStep != "payment" && $checkCapsActive) {
            $this->checkGridDesc();
            Mage::register('checkout_arpayment_order', true);
            $this->loadLayout();
            $model = Mage::getModel('customerconnect/arpayments')->arpaymentQuote();
            $this->renderLayout();
        } else {
            if ($prevStep != "payment") {
                Mage::getSingleton('core/session')->addError("ERROR - AR Payments Search not available");
                if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                    session_write_close();
                    $this->_redirect('customer/account/index');
                }
            }
        }
    }
    
    /**
     * Reset Page when same aged filter column was sorted again
     */    
    public function checkGridDesc()
    {
        $columnIdParam = $this->getRequest()->getParam('sort');
        $valuesParam   = explode('_', $columnIdParam);
        $checkEmpty    = '';
        if (($valuesParam[0] == "aged")) {
            $resetSort = $this->getRequest()->getParam('dir');
            if ($resetSort == "desc") {
                $this->_redirect('customerconnect/arpayments');
            }
        }
    }
    
    /**
     * Invoice Details action 
     */
    public function invoicedetailsAction()
    {
        if ($this->_loadInvoice()) {
            $this->loadLayout()->renderLayout();
        }
        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }
    
    public function detailsAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('customerconnect/customer_arpayments_list')->setTemplate('customerconnect/arpayments/sidebar.phtml')->toHtml());
    }
    
    /**
     * Review Payment Action
     */
    
    public function reviewAction()
    {
        Mage::unregister('checkout_arpayment_order');
        Mage::register('checkout_arpayment_order', true);
        Mage::register('checkout_arpayment_savepayment', true);
        $postparams                         = Mage::App()->getRequest()->getParam('postvals');
        $paymentOptions                     = array();
        $paymentOptions['paymentOnAccount'] = Mage::App()->getRequest()->getParam('paymentOnAccount');
        $paymentOptions['amountLeft']       = Mage::App()->getRequest()->getParam('amountLeft');
        $paymentOptions['allocatedAmount']  = Mage::App()->getRequest()->getParam('allocatedAmount');
        if (!empty($postparams) && ($postparams[0] != "{}")) {
            $model = Mage::getModel('customerconnect/arpayments')->addInvoicesToQuote($postparams[0], $paymentOptions);
        } else if ((trim($postparams[0]) == "{}") && ($paymentOptions['paymentOnAccount'] == "true")) {
            $model = Mage::getModel('customerconnect/arpayments')->addDummyInvoicesToQuote($paymentOptions);
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Customer Connect History Page
     */    
    public function historyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    
    /**
     * AR Payments invoices grid action
     */      
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('customerconnect/customer_arpayments_list_grid')->toHtml() 
            );
    }
    
    
    public function agedgridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('customerconnect/customer_arpayments_aged_grid')->toHtml() 
            );
    }    
    
    
    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id    = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);
        
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        if (!$order->getEccArpaymentsInvoice()) {
            //$this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('sales/order/view', array(
                'order_id' => $order->getId()
            ));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
    
    
    /**
     *  View Payment Details 
     *
     *
     */
    
    public function viewAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            $this->loadLayout();
            $this->renderLayout();
        }
    }
    
    /**
     *  View Print Action
     *
     *
     */
    
    public function printAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            $this->loadLayout('print');
            $this->renderLayout();
        }
    }
    
    
    
    /**
     *  Make a CUID call fro the Invoice Details Page
     *
     *
     */
    private function _loadInvoice()
    {
        $loaded           = false;
        $helper           = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erpAccountNumber = $helper->getErpAccountNumber();
        $invoice          = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('invoice'))));
        if (count($invoice) == 2 && $invoice[0] == $erpAccountNumber && !empty($invoice[1])) {
            $cuid             = Mage::getSingleton("customerconnect/message_request_cuid");
            $messageTypeCheck = $cuid->getHelper("customerconnect/messaging")->getMessageType('CUID');
            if ($cuid->isActive() && $messageTypeCheck) {
                
                $cuid->setAccountNumber($erpAccountNumber)->setInvoiceNumber($invoice[1])->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()))->setType($this->getRequest()->getParam('attribute_type'));
                
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