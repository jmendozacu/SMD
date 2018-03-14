<?php

/**
 * Orders controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Sales_OrderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Check order view availability
     *
     * @param   Epicor_Comm_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId) && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);

        if ($this->_canViewOrder($order)) {
            return $order;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Action for reorder
     */
    public function reorderAction()
    {
        $order = $this->_loadValidOrder();
        /* @var $order Epicor_Comm_Model_Order */
        if (!$order) {
            return;
        }

        if ($order->getErpOrderNumber()) {
            $this->_reorderErp($order);
        } else {
            $this->_reorderLocal($order);
        }
    }

    protected function _reorderErp($order)
    {

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erp_account_number = $helper->getErpAccountNumber();

        $result = $helper->sendOrderRequest($erp_account_number, $order->getErpOrderNumber(), $helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

        $cartHelper = Mage::helper('epicor_common/cart');
        /* @var $cartHelper Epicor_Common_Helper_Cart */

        if (empty($result['order']) || !$cartHelper->processReorder($result['order'])) {

            if (!empty($result['error'])) {
                Mage::getSingleton('core/session')->addError($result['error']);
            }

            if (!Mage::getSingleton('core/session')->getMessages()->getItems()) {
                Mage::getSingleton('core/session')->addError('Failed to build cart for Re-Order request');
            }

            $this->_redirect('checkout/cart/');

            $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('return'));
            if (empty($location)) {
                $location = Mage::getUrl('sales/order/history');
            }

            $this->_redirectUrl($location);
        } else {
            $this->_redirect('checkout/cart/');
        }
    }

    protected function _reorderLocal($order)
    {
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */
        $quote = $cart->getQuote();
        //check the cart options 
        $helper = Mage::helper('epicor_common/cart');
        /* @var $helper Epicor_Common_Helper_Cart */
        $helper->removeFromCartValues();
        $helper->updateExistingCart($quote);
        $items = $order->getItemsCollection();
        $locHelper  = Mage::helper('epicor_comm/locations');
        $locEnabled = $locHelper->isLocationsEnabled();       

        foreach ($items as $item) {
            /* @var $item Mage_Sales_Model_Order_Item */
            try {
                $product = $item->getProduct();
                /* @var $helper Epicor_Common_Helper_Cart */
                
                $locationCode = $item->getEccLocationCode();
                $branchHelper = Mage::helper('epicor_branchpickup');
                if ($branchHelper->isBranchPickupAvailable() && $branchHelper->getSelectedBranch()) {
                    $locationCode = $branchHelper->getSelectedBranch();
                }
                
                $options = array(
                    'qty' => $item->getQtyOrdered(),
                    'location_code' => $locationCode
                );

                if ($locEnabled && isset($options['location_code'])) {
                    $proHelper = Mage::helper('epicor_comm/product');
                   
                    $newQty = $proHelper->getCorrectOrderQty($product, $options['qty'], $locEnabled, $locationCode);
                    if ($newQty['qty'] != $options['qty']) {
                        $options['qty'] = $newQty['qty'];
                        $message = $newQty['message'];
                        Mage::getSingleton('checkout/session')->addSuccess($message);
                    }
                }
                // Addnig this condition as $options['qty'] becomes zero when cart already has max qty allowed
                // addLine method is adding one qty to cart when $options['qty'] is zero
                if ($options['qty'] != 0) {
                    $quote->addLine($product, $options);
                }
            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                } else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                $this->_redirect('*/*/history');
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
                );
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');
    }
     /**
     * check cart merge config option
     */
    public function cartReorderOptionAction()
    {
        $cartMergeAction = Mage::getStoreConfig('sales/reorder/cart_merge_action');
        $cart = Mage::getModel('checkout/cart')->getQuote()->getAllItems();
        $response = array('success' => $cartMergeAction, 'existing_cart_items'=> empty($cart) ? false : true);    
// --SF delete       // if prompt is not an option the clear cart option does not need to be checked
//        if($cartMergeAction != 'prompt'){
//            $customer = Mage::getSingleton('customer/session');
//            $customer->setClearCartInRedirectOption(0);
//        }
        echo json_encode($response);
    }
    
    
    public function nonErpProductCheckAction()
    {
        $params = Mage::app()->getRequest()->getParams();
        $rfqSkus = false;
        if (isset($params['data'])) {
            $rfqSkus = json_decode($params['data']);
        }
        
        $source = isset($params['source']) ? $params['source'] : 'checkout';
        $nonErpItemsEnabled = false;
        $options = Mage::getStoreConfig('epicor_product_config/non_erp_products/options');
        $msgText = Mage::getStoreConfig('epicor_product_config/non_erp_products/' . $source . '_text');
        $guest = false;
        $nonErpItems = false;
        if (Mage::getStoreConfig('epicor_product_config/non_erp_products/enabled') && $options == 'request') {

            $nonErpItemsEnabled = true;
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $guest = Mage::getSingleton('customer/session')->isLoggedIn() ? false : true;
            if ($rfqSkus) {
                foreach ($rfqSkus as $sku) {
                    $productId = Mage::getModel('catalog/product')->getIdBySku($sku);
                    $productTypeId = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'type_id', Mage::app()->getStore()->getStoreId());
                    $productStkType = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'stk_type', Mage::app()->getStore()->getStoreId());
                    if (!$productStkType && $productTypeId != 'configurable') {
                        $nonErpItems = true;
                        break;
                    }
                }
            } else {
                $cart = Mage::getSingleton('checkout/cart');
                /* @var $cart Mage_Checkout_Model_Cart */
                $items = $cart->getItems();
                foreach ($items as $item) {
                    $productTypeId = Mage::getResourceModel('catalog/product')->getAttributeRawValue($item->getProduct()->getId(), 'type_id', Mage::app()->getStore()->getStoreId());
                    $productStkType = Mage::getResourceModel('catalog/product')->getAttributeRawValue($item->getProduct()->getId(), 'stk_type', Mage::app()->getStore()->getStoreId());
                    if (!$productStkType && $productTypeId != 'configurable') {
                        $nonErpItems = true;
                        break;
                    }
                }
            }
        }

        echo json_encode(array(
            'success' => true, 
            'msgText' => $msgText,
            'nonErpItemsEnabled' => $nonErpItemsEnabled, 
            'option' => $options, 
            'nonErpItems' => $nonErpItems,
            'guest' => $guest)
        );
    }

    public function nonErpProductCheckEnabledAction()
    {
        $nonErpProductCheckEnabled = false;
        $options = Mage::getStoreConfig('epicor_product_config/non_erp_products/options');
        $checkoutText = Mage::getStoreConfig('epicor_product_config/non_erp_products/checkout_text');
        if (Mage::getStoreConfig('epicor_product_config/non_erp_products/enabled') && $options == 'request') {
            $nonErpProductCheckEnabled = true;
        }
        
        echo json_encode(array(
            'success' => true, 
            'nonErpProductCheckEnabled' => $nonErpProductCheckEnabled,
            'checkoutText' => $checkoutText)
        );
    }

    public function captureDetailsAction()
    {
            $productSkusJSON = $this->getRequest()->getParam('productSkus');
            $registerAccount = $this->getRequest()->getParam('registerAccount');
            $source = $this->getRequest()->getParam('source');
            if ($productSkusJSON) {
                Mage::unregister('rfq_product_skus');
                Mage::register('rfq_product_skus', $productSkusJSON);
            }
            $data = $this->getRequest()->getParam('data');

            $helper = Mage::helper('epicor_common');
            /* @var $helper Epicor_Common_Helper_Data */

            //don't attempt to register if customer is logged in 
            if ($registerAccount && !Mage::getSingleton('customer/session')->isLoggedIn()) {
                $helper->saveCustomerDetails($data);
            }

            $helper->retrieveNonErpProductsInCart($data, false, $source);
        $this->getResponse()->setBody(json_encode(array('success' => true)));
    }

}

