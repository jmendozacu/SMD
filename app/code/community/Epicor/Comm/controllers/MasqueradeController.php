<?php

/**
 * ERP Account controller controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_MasqueradeController extends Mage_Core_Controller_Front_Action
{

    public function masqueradeAction()
    {
        if ($data = $this->getRequest()->getParams()) {
            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */

            $customer = $customerSession->getCustomer();
            /* @var $customer Epicor_Comm_Model_Customer */

            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */
            
            // reset branch pickup data
            Mage::helper('epicor_branchpickup')->selectBranchPickup(null);
            Mage::helper('epicor_branchpickup')->resetBranchLocationFilter();
            
            if (isset($data['masquerade_as'])) {
                if ($customer->canMasqueradeAs($data['masquerade_as'])) {
                    $helper->startMasquerade($data['masquerade_as']);
                    if (isset($data['cart_action'])) {
                        $this->_processCart($data['cart_action']);
                    }
                } else {
                    $session = Mage::getSingleton('core/session');
                    /* @var $session Mage_Core_Model_Session */
                    $session->addError($this->__('You are not allowed to masquerade as this ERP Account'));
                }
            } else {
                $helper->stopMasquerade();
                if (isset($data['cart_action'])) {
                    $this->_processCart($data['cart_action']);
                }
            }
            if($data['isAjax'] == "true") {
                $result = array(
                    'type' => 'success'
                );                
                $this->getResponse()->setHeader('Content-type', 'application/json');
                $this->getResponse()->setBody(json_encode($result));                
            } else {
              $this->_redirectUrl($helper->urlDecode($data['return_url']));  
            }
        } else {
            exit;
            $this->_redirect('*/*/index');
        }
    }
    
    

    private function _processCart($action)
    {
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */
        $session = Mage::getSingleton('checkout/session');
        /* @var $cart Mage_Checkout_Model_Session */

        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        $cart->getQuote()->setErpAccountId($helper->getErpAccountInfo()->getId());
        Mage::getModel('customer/session')->setCartMsqRegistry(array());
        Mage::getModel('customer/session')->setBsvTriggerTotals(array());
        $cart->getQuote()->setTotalsCollectedFlag(false);
        switch ($action) {
            case'clear':
                Mage::dispatchEvent('checkout_cart_empty', array());
                $cart->truncate()->save();
                $session->setCartWasUpdated(true);
                break;
            case 'reprice':

                $this->_updateCartAddresses();

                $cart->save();
                $session->setCartWasUpdated(true);
                break;
            case 'save':
                // No Actions here... yet
                break;
        }
    }

    private function _updateCartAddresses()
    {
        $commHelper = Mage::helper('epicor_comm');
        /* @var $commHelper Epicor_Comm_Helper_Data */
        $erpAccountInfo = $commHelper->getErpAccountInfo();
        /* @var $erpAccountInfo Epicor_Comm_Model_Customer_Erpaccount */

        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */

        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        $defaultBillingAddressCode = $erpAccountInfo->getDefaultInvoiceAddressCode();
        $defaultShippingAddressCode = $erpAccountInfo->getDefaultDeliveryAddressCode();

        $quote = Mage::getSingleton('checkout/session')->getQuote();

        $billingAddress = $erpAccountInfo->getAddress($defaultBillingAddressCode);

        if ($billingAddress) {
            $erpAddress = $billingAddress->toCustomerAddress($customer, $erpAccountInfo->getId());
            $quote->setBillingAddress(new Mage_Sales_Model_Quote_Address($erpAddress->getData()));
        }

        $shippingAddress = $erpAccountInfo->getAddress($defaultShippingAddressCode);

        if ($shippingAddress) {
            $erpAddress = $shippingAddress->toCustomerAddress($customer, $erpAccountInfo->getId());
            $quote->setShippingAddress(new Mage_Sales_Model_Quote_Address($erpAddress->getData()));
        }
    }

}
