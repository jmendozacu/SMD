<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
include_once('Mage' . DS . 'Checkout' . DS . 'controllers' . DS . 'CartController.php');

/**
 * Shopping cart controller
 */
class Epicor_Comm_CartController extends Mage_Checkout_CartController
{

    protected $_products;

    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {

                $amount = Mage::helper('epicor_comm')->getMinimumOrderAmount($cart->getQuote()->getCustomer()->getErpaccountId());
                $_fromCurr = $cart->getQuote()->getBaseCurrencyCode();
                $_toCurr = Mage::app()->getStore()->getCurrentCurrencyCode();
                $minimumAmount = Mage::helper('epicor_comm')->getCurrencyConvertedAmount($amount, $_fromCurr, $_toCurr);
// 
                $warning = Mage::getStoreConfig('sales/minimum_order/description') ? Mage::getStoreConfig('sales/minimum_order/description') : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

                $cart->getCheckoutSession()->addNotice($warning);
            }
        }
        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this
                ->loadLayout()
                ->_initLayoutMessages('checkout/session')
                ->_initLayoutMessages('catalog/session')
                ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');
    }

    public function addAction()
    {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        $productIds = $this->getRequest()->getParam('products');
        $superGroup = $this->getRequest()->getParam('super_group_locations');
        $configure = $this->getRequest()->getParam('configurelist');

        if (!empty($productIds) || !empty($superGroup)) {
            if(Mage::registry('Epicor_No_Valid_Qty_Selected')){
            // set in observer checkQtySelected. Will be false if locations is off or a valid qty selected for the product
                Mage::getSingleton('checkout/session')->addError($this->__('Please select a valid quantity for this product'));
            }else{                
                try {
                    if (!empty($productIds)) {
                        $this->_addmultiple($productIds, $configure, $params);
                    } else {
                        $product = $this->_initProduct();
                        $this->_addSuperGroup($params, $superGroup, $product);
                    }
                } catch (Mage_Core_Exception $e) {
                    if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    } else {
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    }
                    Mage::logException($e);
                } catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
                }
            }

            $this->_goBack();
        } else {
            if (isset($params['update_config_value'])) {
                $cartItemId = $this->getRequest()->getParam('cart_item_id');
                $superGroup = $this->getRequest()->getParam('super_group');
                $superGroupQty = reset($superGroup);            // return first value in super group array
                $cart = array($cartItemId => array('qty' => $superGroupQty));
                $this->getRequest()->setParam('cart', $cart);
                $this->_updateShoppingCart();
                $this->_goBack();
            } else {
                $locHelper = Mage::helper('epicor_comm/locations');
                $branchHelper = Mage::helper('epicor_branchpickup');
                $locEnabled = $locHelper->isLocationsEnabled();
                if($locEnabled) {
                    $locationCode = $locHelper->getDefaultLocationCode();
                    if (isset($params['location_code'])) {
                        $locationCode = $params['location_code'];
                    } else if ($branchHelper->isBranchPickupAvailable() && $branchHelper->getSelectedBranch()) {
                        $locationCode = $branchHelper->getSelectedBranch();
                    }
                    
                    $helper = Mage::helper('epicor_comm/product');
                    if (isset($params['super_group'])) {
                        $products = array_keys($params['super_group']);
                        foreach($products as $prod) {
                            $product = Mage::getModel('catalog/product')->load($prod);
                            $newQty = $helper->getCorrectOrderQty($product, $params['super_group'][$prod], $locEnabled, $locationCode);
                            //Minimum and Maximum Qty check for product
                            if ($newQty['qty'] != $params['super_group'][$prod]) {
                                $params['super_group'][$prod] = $newQty['qty'];
                                $message = $newQty['message'];
                                Mage::getSingleton('checkout/session')->addSuccess($message); 
                            }
                        }
                    } else if(isset($params['super_attribute'])) {
                        $configurableProduct = Mage::getModel('catalog/product')->load($params['product']);
                        $product = Mage::getSingleton('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $configurableProduct);
                        $quantity=isset($params['qty'])?$params['qty']:1;
                        $newQty = $helper->getCorrectOrderQty($product, $quantity, $locEnabled, $locationCode);
                    } else {
                        $product = Mage::getModel('catalog/product')->load($params['product']);
                        if (!isset($params['qty']) || ($params['qty'] == 0)) {
                            $params['qty'] = 1;
                        }
                        $newQty = $helper->getCorrectOrderQty($product, $params['qty'], $locEnabled, $locationCode);
                    }

                    if (!isset($params['super_group'])) {
                        //Minimum and Maximum Qty check for product
                        if ($newQty['qty'] != $params['qty']) {
                            $params['qty'] = $newQty['qty'];
                            $message = $newQty['message'];
                            Mage::getSingleton('checkout/session')->addSuccess($message); 
                        }
                    }

                    if (isset($params['qty']) && $params['qty'] == 0) {
                        $url = $this->_getRefererUrl();
                        $checkoutHelper = Mage::helper('checkout/cart');
                        if (strpos($url, 'quickorderpad') || !$checkoutHelper->getShouldRedirectToCart()) {
                            $this->_redirectReferer();
                        } else {
                            $this->_redirectUrl($checkoutHelper->getCartUrl());
                        }
                        return;
                    }
                    $this->getRequest()->setParams($params);
                }
                parent::addAction();
            }
        }
    }

    /**
     * Adding multiple products to shopping cart action
     * based on Mage_Checkout_CartController::addAction()
     * see also http://www.magentocommerce.com/boards/viewthread/8610/
     * and http://www.magentocommerce.com/wiki/how_to_overload_a_controller
     */
    public function _addmultiple($productIds, $configure, $params = array())
    {
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Epicor_Comm_Model_Cart */

        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $locHelper Epicor_Comm_Helper_Locations */
        
        $locEnabled = $locHelper->isLocationsEnabled();
        
        $configureProducts = array();
        foreach ($productIds as $productId => $request) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
            
            if ($configure && ($product->getTypeId() == 'configurable' || $helper->productHasCustomOptions($product, true))) {
                $configureProducts[] = $productId;
            } else {
                if (isset($request['multiple'])) {
                    foreach ($request['multiple'] as $key => $mRequest) {
                        if ($locEnabled) {
                            $locationCode = $mRequest['location_code'];
                            $newQty = $helper->getCorrectOrderQty($product, $mRequest['qty'], $locEnabled, $locationCode);
                            //Minimum and Maximum Qty check for product
                            if ($newQty['qty'] != $mRequest['qty']) {
                                $mRequest['qty'] = $newQty['qty'];
                                $params['products'][$productId]['multiple'][$key]['qty']  = $newQty['qty'];
                                $message = $newQty['message'];
                                Mage::getSingleton('checkout/session')->addSuccess($message);
                            }
                        }
                        $requestData = array_merge($params, $mRequest);
                        
                        if (isset($mRequest['super_group_locations']) && $mRequest['super_group_locations']) {
                            $this->_addSuperGroup($requestData, $mRequest['super_group_locations'], $product, $configure, false);
                        } else {
                            if ((is_numeric($mRequest['qty']) && $mRequest['qty'] > 0)
                                || (isset($mRequest['super_group']) && max($mRequest['super_group']) > 0)) {
                                $this->_addProduct($product, $requestData);
                            }
                        }
                    }
                } else {
                    if ($request['super_group_locations']) {
                        $this->_addSuperGroup($request, $request['super_group_locations'], $product, $configure, false);
                    } else {
                        if (is_numeric($request['qty']) && $request['qty'] > 0) {
                            $this->_addProduct($product, $request);
                        }
                    }
                }
            }
            
            if($locEnabled){ 
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $customerId = $customer->getId();
                  $wishlistProduct = Mage::getModel('wishlist/item')->getCollection()                           // remove from wishlist if locations is enabled 
                            ->addFieldToFilter('product_id', array('eq'=>$product->getId()))
                            ->addCustomerIdFilter($customer->getId())
                            ->addStoreData()
                            ->getFirstItem();
                if(!$wishlistProduct->isObjectNew()){
                    $wishlistProduct->delete();
                }
            }
            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
            Mage::getSingleton('checkout/session')->addSuccess($message);
        }

        if ($configure && !empty($configureProducts)) {
            $helper->addConfigureListProducts($configureProducts);
        }

        $cart->save();
    }

    protected function _addProduct($product, $request)
    {
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Epicor_Comm_Model_Cart */

        $eventArgs = array(
            'product' => $product,
            'qty' => isset($request['qty']) ? ($request['qty'] ?: 1) : 1,
            'request' => $request,
            'response' => $this->getResponse(),
            'super_group' => isset($request['super_group']) ? $request['super_group'] : null,
            'bundle_option' => isset($request['bundle_option']) ? $request['bundle_option'] : null,
            'location_code' => isset($request['location_code']) ? $request['location_code'] : ''
        );

        Mage::dispatchEvent('checkout_cart_before_add', $eventArgs);
        $requestInfo = array_merge($request, $eventArgs);
        unset($requestInfo['product']);
        $cart->addProduct($product, $requestInfo);
        Mage::dispatchEvent('checkout_cart_after_add', $eventArgs);
    }

    public function _addSuperGroup($request, $superGroup, $product, $configure = null, $saveCart = true)
    {
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Epicor_Comm_Model_Cart */
        
        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $locHelper Epicor_Comm_Helper_Locations */
        
        $locEnabled = $locHelper->isLocationsEnabled();
        if (isset($request['update_config_value'])) {
            unset($request['update_config_value']);
        }
        foreach ($superGroup as $locationCode => $group) {
            $processedGroup = array();

            foreach ($group as $productId => $qty) {
                if (is_numeric($qty) && !empty($qty)) {
                    if ($locEnabled) {
                        $prod = Mage::getModel('catalog/product')->load($productId);
                        $newQty = $helper->getCorrectOrderQty($prod, $qty, $locEnabled, $locationCode);
                        //Minimum and Maximum Qty check for product
                        if ($newQty['qty'] != $qty) {
                            $qty = $newQty['qty'];
                           $message = $newQty['message'];
                           Mage::getSingleton('checkout/session')->addSuccess($message);
                        }                        
                    }
                    $processedGroup[$productId] = $qty;
                }
            }
            if (array_sum($processedGroup) > 0) {
                $eventArgs = array(
                    'product' => $product,
                    'qty' => (isset($request['qty']) && $request['qty']) ? : 1,
                    'request' => $request,
                    'response' => $this->getResponse(),
                    'super_group' => $processedGroup,
                    'bundle_option' => isset($request['bundle_option']) ? $request['bundle_option'] : null,
                    'location_code' => $locationCode
                );

                Mage::dispatchEvent('checkout_cart_before_add', $eventArgs);
                $cart->addProduct($product, $eventArgs);
                Mage::dispatchEvent('checkout_cart_after_add', $eventArgs);
            }
        }

        if ($saveCart) {
            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
            Mage::getSingleton('checkout/session')->addSuccess($message);

            $cart->save();
        }
    }

    public function estimatePostAction()
    {
        Mage::dispatchEvent('checkout_cart_estimate_shipping_action', array());
        parent::estimatePostAction();
    }

    public function estimateUpdatePostAction()
    {
        Mage::dispatchEvent('checkout_cart_estimate_shipping_action', array());
        $code = (string) $this->getRequest()->getParam('estimate_method');
        if (!empty($code)) {
            $this->_getQuote()->getShippingAddress()->setShippingMethod($code)/* ->collectTotals() */->save();
        }

        Mage::dispatchEvent('checkout_cart_estimate_shipping_update', array('quote' => $this->_getQuote()));
        $this->_goBack();
    }

    /**
     * Empty customer's shopping cart
     */
    protected function _emptyShoppingCart()
    {
        Mage::dispatchEvent('checkout_cart_empty', array());
        parent::_emptyShoppingCart();
    }

    /**
     * Empty customer's shopping cart
     */
    public function couponPostAction()
    {
        Mage::dispatchEvent('checkout_coupon_post', array());
        parent::couponPostAction();
    }

    public function csvuploadAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function importProductCsvAction()
    {
        if (empty($_FILES['import_product_csv_file']['tmp_name'])) {
            Mage::getSingleton('core/session')->addNotice('Please select a file before submitting');
            $this->_redirectReferer();
            return;
        }
        $emptyExistingCart = $this->getRequest()->getParam('replace_cart');

        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        //check if products still to be configured are still to be removed 
        $emptyConfigureProducts = $this->getRequest()->getParam('remove_products_to_be_configured');
        if ($emptyConfigureProducts) {
            $helper->clearConfigureList();
        }

        $products = $helper->processProductCsvUpload($_FILES['import_product_csv_file']['tmp_name']);

        foreach ($products['errors'] as $error) {
            Mage::getSingleton('core/session')->addError($error);
        }

        $configureProducts = array();
        if (!empty($products['products'])) {
            
            $configureProducts = $helper->addCsvProductToCart($products['products'], $emptyExistingCart);
        }

        if (isset($configureProducts['products']) && !empty($configureProducts['products'])) {
            $helper->addConfigureListProducts($configureProducts['products']);
            $helper->addConfigureListQtys($configureProducts['qty']);
            Mage::getSingleton('core/session')->addError('One or more products require configuration before they can be added to the Cart. See list below');
            $this->getResponse()->setRedirect(Mage::getUrl('quickorderpad/form/results', array('csv' => 1)));
        } else {
            $this->_goBack();
        }
    }

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     * @throws Mage_Exception
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart') && !$this->getRequest()->getParam('in_cart') && !$this->getRequest()->getParam('update_config_value') && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
    
    /**
     * Minicart ajax update qty action
     */
    public function ajaxUpdateAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int)$this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getParam('qty');
        $result = array();
        if ($id) {
            try {
                $cart = $this->_getCart();
                $quoteItem = $cart->getQuote()->getItemById($id);
                $product = $quoteItem->getProduct();
                if (isset($qty)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $qty = $filter->filter($qty);
                    
                    $locHelper = Mage::helper('epicor_comm/locations');
                    /* @var $locHelper Epicor_Comm_Helper_Locations */
                    
                    $proHelper = Mage::helper('epicor_comm/product');
                    /* @var $proHelper Epicor_Comm_Helper_Product */
                    
                    $locEnabled = $locHelper->isLocationsEnabled();
                    if ($locEnabled) {
                        $locationCode = $quoteItem->getEccLocationCode();
                        $newQty = $proHelper->getCorrectOrderQty($product, $qty, $locEnabled, $locationCode, true);
                        if ($newQty['qty'] != $qty) {
                            $qty = $newQty['qty'];
                            $message = $newQty['message'];
                        }
                    }
                }

                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }
                if ($qty == 0) {
                    $cart->removeItem($id);
                } else {
                    $quoteItem->setQty($qty)->save();
                }
                $this->_getCart()->save();

                $this->loadLayout();
                $result['content'] = $this->getLayout()->getBlock('minicart_content')->toHtml();

                $result['qty'] = $this->_getCart()->getSummaryQty();

                if (!$quoteItem->getHasError()) {
                    $result['message'] = $this->__('Item was updated successfully.');
                    if ($message) {
                        $result['message'] = $message;
                    }
                } else {
                    $result['notice'] = $quoteItem->getMessage();
                }
                $result['success'] = 1;
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__('Can not save item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!isset($params['qty'])) {
                $params['qty'] = 1;
            }
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
                $locationCode = $quoteItem->getEccLocationCode();
                $product = Mage::getSingleton('catalog/product')->load($params['product']);
                
                $locHelper = Mage::helper('epicor_comm/locations');
                    /* @var $locHelper Epicor_Comm_Helper_Locations */
                    
                $proHelper = Mage::helper('epicor_comm/product');
                /* @var $proHelper Epicor_Comm_Helper_Product */

                $locEnabled = $locHelper->isLocationsEnabled();
                if ($locEnabled) {
                    $newQty = $proHelper->getCorrectOrderQty($product, $params['qty'], $locEnabled, $locationCode, true);
                    if ($newQty['qty'] != $params['qty']) {
                        $params['qty'] = $newQty['qty'];
                        $message = $newQty['message'];
                    }
                }
            }

            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);
            $this->_goBack();
        }
        $this->_redirect('*/*');
    }
}
