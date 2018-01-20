<?php

/**
 * ERP Account controller controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_LocationsController extends Mage_Core_Controller_Front_Action
{

    public function filterAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $helper = Mage::helper('epicor_comm/locations');
            /* @var $helper Epicor_Comm_Helper_Locations */
            $postedLocations = @$data['locations_filter'] ? : array();
            $helper->setCustomerDisplayLocationCodes($postedLocations);
            $this->_redirectUrl($helper->urlDecode($data['return_url']));
        }
    }

    public function addToCartFromMyOrdersWidgetAction()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        
        $orderItems = $this->getRequest()->getParam('order_items');
        $products = array();
        foreach ($orderItems as $orderItem) {
            $salesOrderItem = Mage::getModel('sales/order_item')->load($orderItem);
            /* @var $salesOrderItem Mage_Sales_Model_Order_Item */
            $product = $salesOrderItem->getProduct();
            /* @var $product Epicor_Comm_Model_Product */
            $sku = $helper->stripProductCodeUOM($product->getSku());
            $products[] = array(
                'sku' => $sku,
                'qty' => 1
            );
        }
        
        $this->_massiveAddFromSku($products, 'last_ordered_items');
    }

    public function addToCartFromWishlistAction()
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        
        $customer = Mage::getModel('customer/session');
        $wishlist = Mage::getModel('wishlist/item')->getCollection()
            ->addCustomerIdFilter($customer->getId())
            ->addStoreData();
        $qty = $this->getRequest()->getParam('qty');
        
        $products = array();
        foreach ($wishlist as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            /* @var $product Epicor_Comm_Model_Product */
            $sku = $helper->stripProductCodeUOM($product->getSku());
            $products[] = array(
                'sku' => $sku,
                'qty' => isset($qty[$item->getId()]) ? trim($qty[$item->getId()]) : 1,
                'wishlist_item_id' => $item->getId()
            );
        }
        
        $this->_massiveAddFromSku($products, 'wishlist');
    }

    protected function _massiveAddFromSku($skus = array(), $trigger = '')
    {
        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */
        
        $configureProducts = $helper->massiveAddFromSku($skus, $trigger);

        if (isset($configureProducts['products']) && !empty($configureProducts['products'])) {
            if (count($configureProducts['products']) == 1) {
                $productId = array_pop($configureProducts['products']);
                $product = Mage::getModel('catalog/product')->load($productId);
                $this->_redirect($product->getUrlKey() . '.html');
            } else {
                $helper->addConfigureListProducts($configureProducts['products']);
                $helper->addConfigureListQtys($configureProducts['qty']);
                $this->getRequest()->setParam('csv', 1);
                Mage::getSingleton('core/session')->addError('One or more products require configuration before they can be added to the Cart. See list below');
                $this->loadLayout()->renderLayout();
            }
        } else {
            $this->_redirect('checkout/cart');
        }
    }

}
