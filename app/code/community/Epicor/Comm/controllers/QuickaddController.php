<?php

/**
 * Quick add to basket / wishlist controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_QuickaddController extends Mage_Core_Controller_Front_Action
{

    public function autocompleteAction()
    {
        $sku = $this->getRequest()->getParam('sku', false);       
        $this->getResponse()->setBody($this->getLayout()->createBlock('epicor_comm/cart_quickadd_autocomplete')->toHtml());
    }

    public function addAction()
    {
        $data = $this->getRequest()->getPost();
        $locHelper = Mage::helper('epicor_comm/locations');
        $locEnabled = $locHelper->isLocationsEnabled();
        if ($data && isset($data['sku'])) {
            $productId = isset($data['product_id']) ? $data['product_id'] : '';
            $product = $this->_initProduct($data['sku'], $productId);
        }
    
        
        
        if($locEnabled){
            /* @var $helper Epicor_Comm_Helper_Locations */
            $stockVisibility = Mage::getStoreConfig('epicor_comm_locations/global/stockvisibility');
            if(!$data['location_code'] || in_array($stockVisibility, (array('all_source_locations', 'default')))){                 // if default location code required         
                $branchHelper = Mage::helper('epicor_branchpickup');
                $defaultLocationCode = ($branchHelper->getSelectedBranch()) ? $branchHelper->getSelectedBranch() : Mage::helper('epicor_comm/locations')->getDefaultLocationCode();
                $selectedBranch = $branchHelper->getSelectedBranch();
                if ($branchHelper->isBranchPickupAvailable() && $selectedBranch) {
                    $defaultLocationCode = $branchHelper->getSelectedBranch();
                }
                $data['location_code'] = $defaultLocationCode;   
                $allowed = array_keys($locHelper->getCustomerAllowedLocations());
                if ($selectedBranch && (!$locHelper->getLocation($selectedBranch)->getLocationVisible() || !in_array($selectedBranch, $allowed))) {
                    $error = $this->__('Product %s cannot be added to cart as it is not valid', $data['sku']);
                    Mage::getSingleton('core/session')->addError($error);
                    $this->_redirectReferer();
                    return;
                }
            }
            
            $proHelper = Mage::helper('epicor_comm/product');
            
            if (isset($data['super_group']) && !empty($data['super_group']) && $data['super_group'] != "") {
                $_product = Mage::getModel('catalog/product')->load($data['super_group']);
                $newQty = $proHelper->getCorrectOrderQty($_product, $data['qty'], $locEnabled, $data['location_code']);
            } else if ($product) {
                $newQty = $proHelper->getCorrectOrderQty($product, $data['qty'], $locEnabled, $data['location_code']);
            }
            //Minimum and Maximum Qty check for product
            if ($newQty['qty'] != $data['qty']) {
                $data['qty'] = $newQty['qty'];
                $message = $newQty['message'];
                Mage::getSingleton('checkout/session')->addSuccess($message);
                if ($newQty['qty'] == 0) {
                    $this->_redirectReferer();
                    return;
                }
            }

        }
    
        // check sku is valid for current contract
        $listsHelper = Mage::helper('epicor_lists/frontend_product');
        /* @var $helper Epicor_Lists_Helper_Frontend_Product */

        if ($listsHelper->listsEnabled()) {
            $contractHelper = Mage::helper('epicor_lists/frontend_contract');
            /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
            if ($listsHelper->hasFilterableLists() || $contractHelper->mustFilterByContract()) {
                $productIds = explode(',', $listsHelper->getActiveListsProductIds());
                $skuProduct = Mage::getModel('catalog/product')->getIdBySku($data['sku']);
                $error = false;
                if (empty($skuProduct)) {
                    $error = $this->__('Product %s does not exist', $data['sku']);
                    Mage::getSingleton('core/session')->addError($error);
                } else if (in_array($skuProduct, $productIds) == false) {
                    $error = $this->__('Product %s cannot be added to cart as it is not valid', $data['sku']);
                    Mage::getSingleton('core/session')->addError($error);
                }

                if ($error) {
                    $this->_redirectReferer();
                    return;
                }
            }
        }
        
        $redirect = '';
        try {
            if ($product) {
                if (!Mage::getStoreConfigFlag('cataloginventory/options/show_out_of_stock')) {
                    $collectionData[$product->getEntityId()] = $product;
                    $location_code=(isset($data['location_code']))?$data['location_code']:null;
                    $allow = Mage::helper('epicor_comm')->createMsqRequest($collectionData, 'quickadd', '', '', $location_code);
                    if (!$allow) {
                        $error = $this->__('Product %s currently out of stock', $product->getSku());
                        Mage::getSingleton('core/session')->addError($error);
                        $this->_redirectReferer();
                        return;
                    }
                } else {
                    $this->_checkProduct($product, $data['qty']);
                }
                if($product->getTypeId() == 'grouped' && isset($data['super_group']) && !$product->getStockItem()->getBackorders()){
                    $newclass=new Epicor_Comm_Block_Catalog_Product_View_Type_Grouped_Locations();
                    $primary = $newclass->getPrimaryItems($product); 
                    $uomBeforeLoc = $newclass->getPrimarySort() == 'uom' ? true : false;
                    foreach ($primary as $prime) {
                                          $secondary = $newclass->getSecondaryItems($product, $prime);
                        foreach ($secondary as $second){
                            $childProduct = $uomBeforeLoc ? $prime : $second;
                           $is_salable=$childProduct->isAvailable();
                           if($data['super_group']==$childProduct->getEntityId()){
                               $product->setData('is_salable', $is_salable);
                           }
                        }
                    }
                }
                if ($product->IsSalable()) {
                    $productHelper = Mage::helper('epicor_comm/product');
                    /* @var $productHelper Epicor_Comm_Helper_Product */

                    if ($product->getConfigurator() || $product->getTypeId() == 'configurable' || $productHelper->productHasCustomOptions($product)) {
                        $error = $this->__('Product %s requires configuration before it can be added to the Cart', $product->getSku());
                        Mage::getSingleton('core/session')->addError($error);
                        $redirect = $product->getUrlModel()->getUrl($product, array('_query' => array('qty' => $data['qty'])));
                        #$redirect = $product->getProductUrl();
                    } else if ($product->getTypeId() == 'grouped' && (!isset($data['super_group']) || empty($data['super_group']))) {
                        $error = $this->__('Product %s cannot be added to the cart, please choose a child product', $product->getSku());
                        Mage::getSingleton('core/session')->addError($error);
                        $redirect = $product->getProductUrl();
                    } else {
                        if ($data['target'] == 'basket') {
                            $cart = Mage::getSingleton('checkout/cart');
                            /* @var $cart Mage_Checkout_Model_Cart */

                            if (isset($data['super_group'])) {
                                $data['super_group'] = array(
                                    $data['super_group'] => $data['qty']
                                );
                            }
                            $cart->getQuote()->addOrUpdateLine($product, $data);
                            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
                            Mage::getSingleton('checkout/session')->addSuccess($message);
                            $cart->save();
                        } else if ($data['target'] == 'wishlist') {
                            $this->_addToWishlist($product, $data['qty']);
                        } else {
                            Mage::getSingleton('core/session')->addError('Could not process add request, no destination chosen');
                        }
                    }
                } else {
                    Mage::getSingleton('core/session')->addError('Product not currently available');
                }
            } else {
                Mage::getSingleton('core/session')->addError('Product SKU does not exist');
            }
        } catch (Exception $e) {
            // store the error in the session here
            if (!Mage::registry('quote_session_error_set')) {
                $session = Mage::getSingleton('core/session');
                /* @var $session Mage_Core_Model_Session */
                $session->addError($e->getMessage());
            }
        }
        

        if (empty($redirect)) {
            $this->_redirectReferer();
        } else {
            $this->getResponse()->setRedirect($redirect);
        }
    }

    /**
     * Checks the prodcut using an MSQ
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param integer $qty
     */
    private function _checkProduct(&$product, $qty)
    {
        $msq = Mage::getModel('epicor_comm/message_request_msq');
        if ($msq->isActive()) {
            $msq->addProduct($product, $qty);
            $msq->sendMessage();
        }
    }

    /**
     * Adds a product to the wishlist
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param integer $qty
     */
    private function _addToWishlist($product, $qty)
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            Mage::throwException('Could not update wishlist');
        }

        $requestParams = array(
            'product' => $product->getId(),
            'qty' => $qty
        );

        $buyRequest = new Varien_Object($requestParams);

        $result = $wishlist->addNewItem($product, $buyRequest);
        if (is_string($result)) {
            Mage::throwException($result);
        }
        $wishlist->save();

        Mage::dispatchEvent(
                'wishlist_add_product', array(
            'wishlist' => $wishlist,
            'product' => $product,
            'item' => $result
                )
        );

        /**
         *  Set referer to avoid referring to the compare popup window
         */
        Mage::helper('wishlist')->calculate();

        Mage::getSingleton('core/session')->addSuccess($product->getName() . ' has been added to your wishlist');
    }

    /**
     * Initialize product instance from request data
     *
     * @param string $sku - SKU to load
     * 
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($sku, $productId = '')
    {
        $product = false;
        if ($sku || $productId) {
            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */

            $product = Mage::getModel('catalog/product');
            /* @var $product Epicor_Comm_Helper_Product */

            if ($productId) {
                $product->load($productId);
            }

            if ($sku && $product->isObjectNew()) {
                $product = $helper->findProductBySku($sku, '', false);
            }
        }

        return $product;
    }

    /**
     * Retrieve wishlist object
     * 
     * (Taken from the Wishlist index controller)
     * 
     * @param int $wishlistId
     * @return Mage_Wishlist_Model_Wishlist|bool
     */
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                        Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e, Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
    }
    public function nonAutoLocationsAction(){
        //get all locations for customer/product
         $existingSku = Mage::registry('sku_in_autocomplete');
        $sku = $this->getRequest()->getParam('sku');
        $locations = array();      
        $productId = '';  
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
        /* @var $product Epicor_Comm_Catalog_Model_Product */
        if($product){            
            $productId = $product->getId();
            $locations = Mage::helper('epicor_comm/locations')->getLocationsArray($product);
            $message = 'success';
            if(empty($locations)){
                $message = 'nolocations';
            }else{
                $message = 'success';
            }
        }else{
            $message = 'noproduct';
        }
         
        echo json_encode(array('message'=>$message,'locations'=>$locations,'productid' => $productId));
    }
}
