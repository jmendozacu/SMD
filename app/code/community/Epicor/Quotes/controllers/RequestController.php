<?php

class Epicor_Quotes_RequestController extends Mage_Core_Controller_Front_Action
{

    public function testAction()
    {
        $cron = new Epicor_Quotes_Model_Cron();

        $cron->checkedExpired();
    }

    /**
     * Default Page
     */
    public function indexAction()
    {
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function submitAction()
    {

        $success = $this->__('Quote was submitted for review.');
        $error = $this->__('Quote failed to be submitted for review.');

        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
        $eccSelectedContract = $contractHelper->getSelectedContractCode();

        if (!$customerSession->authenticate($this)) {
            return;
        }

        try {
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $erpAccount = $commHelper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $daysTillExpired = Mage::getStoreConfig('epicor_quotes/general/days_till_expired') ? : 5;
           
            $comments = Mage::app()->getRequest()->getParam('comment');
            $quote = Mage::getModel('quotes/quote');
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->addCustomerId($customer->getId());
            $quote->setErpAccountId($erpAccount->getId());
            $quote->setCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
            
            $customerGlobal = Mage::getStoreConfigFlag('epicor_quotes/general/allow_customer_global');
            if ($customer->isCustomer() && $customerGlobal) {
                $isGlobal = Mage::app()->getRequest()->getParam('is_global') == 1 ? true : false;
                $quote->setIsGlobal($isGlobal);
            }
            
            if ($commHelper->isMasquerading()) {
                $quote->setIsGlobal(true);
            }

            if (!empty($comments['quote'])) {
                $quote->addNote($comments['quote']);
            }

            $quote->setExpires(strtotime('+' . $daysTillExpired . ' days'));
            $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_PENDING_RESPONSE);
            $quote->setContractCode($eccSelectedContract);

            $sessionQuote = Mage::getSingleton('checkout/session')->getQuote();
            /* @var $sessionQuote Mage_Sales_Model_Quote */

            $productModel = Mage::getModel('catalog/product');
            /* @var $productModel Epicor_Comm_Model_Product */
            
            foreach ($sessionQuote->getAllItems() as $product) {
                
                $productId = $productModel->getIdBySku($product->getSku());
                /* @var $product Mage_Sales_Model_Quote_Item */
                $quoteProduct = Mage::getModel('quotes/quote_product');
                $quoteProduct->setProductId($productId);
                $quoteProduct->setOrigQty($product->getQty());
                $quoteProduct->setOrigPrice($product->getPrice());
                $quoteProduct->setNewQty($product->getQty());
                $quoteProduct->setNewPrice($product->getPrice());
                $quoteProduct->setNote($comments[$product->getId()]);
                $quoteProduct->setLocationCode($product->getEccLocationCode());
                $quoteProduct->setContractCode($product->getEccContractCode());

                $helper = Mage::helper('epicor_comm');
                /* @var $helper Epicor_Comm_Helper_Data */

                $options = $helper->getItemProductOptions($product);

                if (isset($options['options'])) {
                    $quoteProduct->setOptions(serialize($options['options']));
                }

                $quote->addItem($quoteProduct);
            }
            
            $quote->setStoreId(Mage::app()->getStore()->getId());
            
            $quote->save();

            $basket = Mage::getSingleton('checkout/cart');
            $basket->truncate()->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            Mage::getSingleton('core/session')->addSuccess($success);
            $this->_redirectUrl(Mage::getUrl('epicor_quotes/manage/view/', array('id' => $quote->getId())));
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($error. $e->getMessage()) ;
            $this->_redirectUrl(Mage::getUrl('checkout/cart'));
        } catch (Mage_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirectUrl(Mage::getUrl('checkout/cart'));
        }
    }

}
