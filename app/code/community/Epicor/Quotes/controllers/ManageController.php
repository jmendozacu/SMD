<?php

class Epicor_Quotes_ManageController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $error = true;
        $errorMsg = $this->__('Error trying to retrieve Quote');
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        try {
            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */
            if ($quote->canBeAccessedByCustomer($customer)) {

                Mage::register('quote', $quote);
                $error = false;

                $this->loadLayout();
                $this->renderLayout();
            } else {
                $errorMsg .= $this->__(': You do not have permission to access this quote');
                throw new Exception('Invalid customer');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        }
        if ($error) {
            $this->_redirectReferer();
        }
    }

    public function acceptAction()
    {
        $successMsg = $this->__('Quote has been accepted');
        $errorMsg = $this->__('Error has occurred while accepting the quote');
        $error = true;
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        try {
            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            if (!$quote->canBeAccessedByCustomer($customer)) {
                $errorMsg .= $this->__(': You do not have permission to access this quote');
                throw new Exception('Invalid customer');
            }

            if (!$quote->isAcceptable()) {
                $errorMsg .= $this->__(': Quote cannot be Accepted');
                throw new Exception('Quote cannot be Accepted');
            }

            if ($quote->productsSaleable()) {
                $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_QUOTE_ACCEPTED);
                $quote->save();

                Mage::getSingleton('core/session')->addSuccess($this->__($successMsg));
                $error = false;
            } else {
                $errorMsg .= $this->__('Could not accept quote, one or more products are no longer available');
                throw new Exception('Could not accept quote, one or more products are no longer available');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        }
        
        if ($error) {
            $this->_redirectReferer();
        } else {
            if ($quote->setQuoteAsCart()) {
                $this->_redirect('checkout/cart');
            } else {
                $this->_redirect('quotes/manage/accept', array('id' => $quote->getId()));
            }
        }
    }

    public function rejectAction()
    {
        $successMsg = $this->__('Quote has been rejected');
        $errorMsg = $this->__('Error has occurred while rejecting the quote');

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        try {

            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            if (!$quote->canBeAccessedByCustomer($customer)) {
                $errorMsg .= $this->__(': You do not have permission to access this quote');
                throw new Exception('Invalid customer');
            }

            $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_QUOTE_REJECTED_CUSTOMER);
            $quote->save();

            Mage::getSingleton('core/session')->addSuccess($successMsg);
            $error = false;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        }

        $this->_redirectReferer();
    }
    public function saveDuplicateAction()
    {
        $success = $this->__('Quote was duplicated and submitted for review.');
        $error = $this->__('Quote failed to be submitted for review.');
        $currentQuoteId = $this->getRequest()->getParam('id');
//        $currentQuote = Mage::getModel('quotes/quote')->load($currentQuoteId);
        $currentQuoteProductlines = Mage::getModel('quotes/quote_product')->getCollection();
        $currentQuoteProductlines->addFieldToFilter('quote_id', $currentQuoteId);
        
        /* @var $currentQuoteProductlines Mage_Core_Model_Mysql4_Collection_Abstract */
        
        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        if (!$customerSession->authenticate($this)) {
            return;
        }

        try {
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $erpAccount = $commHelper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $daysTillExpired = Mage::getStoreConfig('epicor_quotes/general/days_till_expired') ? : 5;
           
            $duplicateQuote = Mage::getModel('quotes/quote');
            /* @var $quote Epicor_Quotes_Model_Quote */
            $duplicateQuote->addCustomerId($customer->getId());
            $duplicateQuote->setErpAccountId($erpAccount->getId());
            $duplicateQuote->setCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
            
            $customerGlobal = Mage::getStoreConfigFlag('epicor_quotes/general/allow_customer_global');
            if ($customer->isCustomer() && $customerGlobal) {
                $isGlobal = Mage::app()->getRequest()->getParam('is_global') == 1 ? true : false;
                $duplicateQuote->setIsGlobal($isGlobal);
            }
            
            if ($commHelper->isMasquerading()) {
                $duplicateQuote->setIsGlobal(true);
            }
            
            // save first user comment only
            $firstNote = Mage::getModel('quotes/quote_note')->getCollection()
                    ->addFieldToFilter('quote_id', $currentQuoteId)
                    ->addFieldToFilter('admin_id', array(array('null' => true), 0))
                    ->setOrder('created_at', 'ASC')->getFirstItem();
            /* @var $firstNote Epicor_Quotes_Model_Quote_Note */
            
            if($firstNote){
                $duplicateQuote->addNote($firstNote->getNote());
            }
            
            
            $duplicateQuote->setExpires(strtotime('+' . $daysTillExpired . ' days'));
            $duplicateQuote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_PENDING_RESPONSE);
            
            foreach ($currentQuoteProductlines->getItems() as $product) {
                
                
                $productId = $product->getProductId();
                /* @var $product Epicor_Quotes_Model_Quote_Product */
                /* @var $quoteProduct Epicor_Quotes_Model_Quote_Product */
                $quoteProduct = Mage::getModel('quotes/quote_product');
                $quoteProduct->setProductId($productId);
                $quoteProduct->setOrigQty($product->getOrigQty());
                $quoteProduct->setOrigPrice($product->getOrigPrice());
                $quoteProduct->setNewQty($product->getNewQty());
                $quoteProduct->setNewPrice($product->getNewPrice());
                $quoteProduct->setNote($product->getNote());
                $quoteProduct->setLocationCode($product->getLocationCode());

                $options = $product->getOptions();
                if (isset($options)) {
                    $quoteProduct->setOptions($product->getOptions());
                }

                $duplicateQuote->addItem($quoteProduct);
            }
            
            $duplicateQuote->setStoreId(Mage::app()->getStore()->getId());
            
            $duplicateQuote->save();

            Mage::getSingleton('core/session')->addSuccess($success);
            $this->_redirectUrl(Mage::getUrl('epicor_quotes/manage/view/', array('id' => $duplicateQuote->getId())));
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($error. $e->getMessage()) ;
            $this->_redirectUrl(Mage::getUrl('epicor_quotes/manage/view/', array('id' =>$currentQuoteId )));
        } catch (Mage_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirectUrl(Mage::getUrl('epicor_quotes/manage/view/', array('id' =>$currentQuoteId )));
        }
    }

    public function updateAction()
    {
       
        $successMsg = $this->__('Options updated successfully');
        $errorMsg = $this->__('An error has occurred while updating quote options');

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        if ($data = $this->getRequest()->getPost()) {
            try {

                $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
                /* @var $quote Epicor_Quotes_Model_Quote */
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                /* @var $customer Epicor_Comm_Model_Customer */

                if (!$quote->canBeAccessedByCustomer($customer)) {
                    $errorMsg .= $this->__(': You do not have permission to access this quote');
                    throw new Exception('Invalid customer');
                }

                $customerGlobal = Mage::getStoreConfigFlag('epicor_quotes/general/allow_customer_global');
                if ($customer->isCustomer() && $customerGlobal) {
                    $quote->setIsGlobal(isset($data['is_global']) ? true : false);
                }
                
                $quote->setSendCustomerComments(isset($data['send_comments']) ? true : false);
                $quote->setSendCustomerReminders(isset($data['send_reminders']) ? true : false);
                $quote->setSendCustomerUpdates(isset($data['send_updates']) ? true : false);
                $quote->save();

                Mage::getSingleton('core/session')->addSuccess($successMsg);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($errorMsg);
            } catch (Mage_Exception $e) {
                Mage::getSingleton('core/session')->addError($errorMsg);
            }
        }
        $this->_redirectReferer();
    }

    public function newnoteAction()
    {
        $successMsg = $this->__('New comment has been added to the quote.');
        $errorMsg = $this->__('Error has occurred while adding the new comment to this quote.');

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        try {

            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            if (!$quote->canBeAccessedByCustomer($customer)) {
                $errorMsg .= $this->__(': You do not have permission to access this quote');
                throw new Exception('Invalid customer');
            }

            $note = $this->getRequest()->get('note');
            if (!$note) {
                $errorMsg = $this->__('Comment was empty. Please try again.');
                throw new Exception('No Note found');
            }

            $quote->addNote($note, null, true, false, true);
            
            $quote->save();

            Mage::getSingleton('core/session')->addSuccess($successMsg);
            $error = false;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        }

        $this->_redirectReferer();
    }

    public function resubmitAction()
    {
        $successMsg = $this->__('Quote re-submitted for review');
        $errorMsg = $this->__('Error has occurred while re-submitting this quote');

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        try {

            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            if (!$quote->canBeAccessedByCustomer($customer)) {
                $errorMsg .= $this->__(': You do not have permission to access this quote');
                throw new Exception('Invalid customer');
            }

            $note = $this->getRequest()->get('note');

            if (!$note) {
                $errorMsg = $this->__('Comment was empty. Please try again.');
                throw new Exception('No Note found');
            }

            $quote->addNote($note);
            $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_PENDING_RESPONSE);
            $quote->save();

            $cart = Mage::getSingleton('checkout/cart');
            $cartQuote = $cart->getQuote();

            if ($cartQuote->getEpicorQuoteId() == $quote->getId()) {
                $cart->truncate()->save();
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                $successMsg .= $this->__(' The quote was also removed from your Cart');
            }

            Mage::getSingleton('core/session')->addSuccess($successMsg);
            $error = false;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg . ':' . $e->getMessage());
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg . ':' . $e->getMessage());
        }

        $this->_redirectReferer();
    }

}
