<?php
/**
 * Copyright (c) 2016, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *   names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * Created by PhpStorm.
 * User: rone ren <rone@silksoftware.com>
 * Date: 17-3-14
 * Time: 15:30
 */
class Silk_Retailer_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function addFavouritesAction()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if(empty($customerId)){
            $data['status'] = 'login';
            $data['url'] = Mage::getUrl('customer/account/login');
	
		$sess = Mage::getSingleton('core/session')
                        ->setData('addProduct',(int)$this->getRequest()->getParam('product'))
			->setData('flag','wishlist');


            $json = Mage::helper('core')->jsonEncode($data);
            $this->getResponse()->setBody($json);
            return;
        }

        $wishlist = Mage::getModel('wishlist/wishlist');
        $wishlist->loadByCustomer($customerId, true);

        $productId = (int)$this->getRequest()->getParam('product');
	//var_dump($this->getRequest());	

        if (!$productId) {
            $data['status'] = 'Failed';
            $data['error'] = 'No Product Id.';
        } else {
            $product = Mage::getModel('catalog/product')->load($productId);
            $buyRequest = new Varien_Object();

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist' => $wishlist,
                    'product' => $product,
                    'item' => $result
                )
            );
            $data['status'] = 'Success';
            $data['url'] = Mage::getUrl('wishlist');
        }
        $json = Mage::helper('core')->jsonEncode($data);
        $this->getResponse()->setBody($json);
	//$this->_redirect($product->getUrlPath());
	//$this->_redirect('wishlist');
	//return;
    }

    public function addBasketAction()
    {
        $cart = Mage::getSingleton('checkout/cart');
        $params  = $this->getRequest()->getParams();
	//Mage::log($params['qty'],null, 'json_data.log',true);
        $retailerHp = Mage::helper('silk_retailer');
	//$parentSku = $params['parentId'];
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if(empty($customerId)){
            $data['status'] = 'login';
            $data['url'] = Mage::getUrl('customer/account/login');
	/**	 $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($params['product']);
		$cart->addProduct($product, $params['qty']);
		$cart->save();
	**/
		$sess = Mage::getSingleton('core/session')
			->setData('addProduct',$params['product'])
			->setData('parentId',$params['parentId'])
			->setData('flag','cart');
		//Mage::log($sess->getData('addProduct').'add',null, 'json_data.log', true);
		//$sess->unsetData('addProduct');
		//Mage::log($sess->getData('addProduct'),null, 'json_data.log', true);
        } else if (empty($params['qty']) || empty($params['product'])) {
            $data['status'] = 'Failed';
            $data['error'] = 'Wrong Params1';
        } else {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($params['product']);
            if ($product->getId()) {
                $data['status'] = 'Success';
                $data['qty'] = $params['qty'];
                $data['name'] = Mage::helper('catalog/output')->productAttribute($product, $product->getName(), 'name');
                $isSample = $product->getIsSample();
                $validateSample = true;

                if ($isSample) {
                    $validateSample = $retailerHp->validateSampleNumber($product, $params['qty']);
                }

                if ($validateSample) {
                    try {
                        $cart->addProduct($product, $params['qty']);
                        $cart->save();
                    } catch (\Exception $e) {
                        $data['status'] = 'Failed';
                        $data['error'] = $e->getMessage();
                    }
			//$this->_redirect($product->getUrlPath());
			//return;
                } else {
                    $data['status'] = 'Failed';
                    $data['error'] = $retailerHp->getSampleErrorMsg();
                }
            } else {
                $data['status'] = 'Failed';
                $data['error'] = 'Wrong Params2';
            }
        }
/**
	if($flag){
		//$this->_redirect($product->getUrlPath());
		$parentProduct = Mage::getModel('catalog/product')->load($params['amp;parentId']);
		//echo $params['amp;arentId'];
		$this->_redirect($parentProduct->getUrlPath());
		$flag = false;
		return;
	}
**/
        $json = Mage::helper('core')->jsonEncode($data);
        $this->getResponse()->setBody($json);
	//$this->_redirect($product->getUrlPath());
    }

    public function addAllToBasketAction(){
        $params  = $this->getRequest()->getParams();
        $data = [];
        $data['status'] = 'Success';
        $faildProducts = [];
        if(isset($params['products']) && $params['products']){
            foreach ($params['products'] as $productId=>$items){
                if(isset($items['multiple']) && $items['multiple']){
                    foreach ($items['multiple'] as $item){
                        $product = Mage::getModel('catalog/product')->load($productId);
                        $poqtyStock = $product->getData('poqtyone');
                        $helper = Mage::helper('epicor_comm/messaging');
                        $helper->sendMsq($product, 'product_details');
                        $maxQty = $poqtyStock + $product->getStockLevel();
                        if($item['qty'] > $maxQty){
                            $data['status'] = 'Failed';

                            $faildProducts[$item['product']] = $maxQty.$product->getData('uom');
                        }
                    }
                }
            }
        }
        $data['faild_products'] = $faildProducts;
        $json = Mage::helper('core')->jsonEncode($data);
        $this->getResponse()->setBody($json);

    }

    public function quickBuyAction(){
        $cart = Mage::getSingleton('checkout/cart');
        $params  = $this->getRequest()->getParams();
        $retailerHp = Mage::helper('silk_retailer');
        $data = [];
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if(empty($customerId)){
            $data['status'] = 'login';
            $data['url'] = Mage::getUrl('customer/account/login');
        } else if (empty($params['qty']) || empty($params['product'])) {
            $data['status'] = 'Failed';
            $data['error'] = 'Wrong Params1';
        } else {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($params['product']);

            if ($product->getId()) {
                $isSample = $product->getIsSample();
                $validateSample = true;

                if ($isSample) {
                    $validateSample = $retailerHp->validateSampleNumber($product, $params['qty']);
                }

                if ($validateSample) {
                    try{
                        $cart->addProduct($product, $params['qty']);
                        $cart->save();
                        $data['status'] = 'Success';
                    } catch (Exception $e) {
                        $data['status'] = 'Failed';
                        $data['error'] = $e->getMessage();
                    }
                } else {
                    $data['status'] = 'Failed';
                    $data['error'] = $retailerHp->getSampleErrorMsg();
                }
                $json = Mage::helper('core')->jsonEncode($data);
                $this->getResponse()->setBody($json);
            } else {
                $this->_redirect('');
            }
        }
    }

    public function getPostcodeAction()
    {
        $checkout = Mage::getSingleton('checkout/session');
        $quote = $checkout->getQuote();
        $data['zip'] = $quote->getShippingAddress()->getPostcode();
        $json = Mage::helper('core')->jsonEncode($data);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($json);
    }

    public function preDispatch()
    {
        parent::preDispatch();
        // if not allow guest action
	/**
        if (!Mage::getStoreConfigFlag(Mage_Checkout_Helper_Data::XML_PATH_GUEST_CHECKOUT, Mage::app()->getStore()->getStoreId()) &&
            !Mage::getSingleton('customer/session')->isLoggedIn()) {
            if ($this->getRequest()->isAjax()) {
                $data['status'] = 'login';
                $data['url'] = Mage::getUrl('customer/account/login');
                $json = Mage::helper('core')->jsonEncode($data);
		
                $this->getResponse()->setHeader('Content-type', 'application/json');
//                $this->getResponse()->setBody($json);
//                return $this;
		//Mage::log($json, null, 'json_data.log');
                echo $json;
                exit;
            } else {
                return $this->_redirect('/customer/account/login');
            }
        }
	**/
        return $this;
    }

    protected function _refreshMiniCart()
    {
//        $block = $this->getLayout()->createBlock('checkout/cart_minicart', 'minicart');
//        $block->setTemplate('checkout/cart/minicart.phtml');
//        $data['head'] = $block->toHtml();
//        $data['count'] = Mage::getSingleton('checkout/session')->getQuote()->getItemsCount();
//
//        $block = $this->getLayout()->createBlock('checkout/cart_sidebar', 'minicart_content');
//        $block->setTemplate('epicor_comm/checkout/cart/minicart/items.phtml');
//
//        $data['content'] = $block->toHtml();
//
//        return $data;
//        $data['content'] = $block->toHtml();
//        $json = Mage::helper('core')->jsonEncode($data);
//        $this->getResponse()->setHeader('Content-type', 'application/json');
//        $this->getResponse()->setBody($json);

    }

    public function refreshMiniCartAction()
    {
        $this->loadLayout();
        $this->renderLayout();
//        Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());

    }
}
