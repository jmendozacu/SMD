<?php

/**
 * Configurable Productsz Controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_ConfigurableproductsController extends Mage_Core_Controller_Front_Action
{

    public function stockandpriceAction()
    {
        
        $productId = (int) $this->getRequest()->getParam('product');
        $params = new Varien_Object($this->getRequest()->getParams());
        $childProduct = false;
        if ($productId) {
            $parentProduct = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($parentProduct->getId()) {
                $configurableProducts = $parentProduct->getTypeInstance(true)
                        ->processConfiguration($params, $parentProduct);
                if($configurableProducts){
                    foreach($configurableProducts as $product){
                        if($product->getParentId() && $product->getTypeId() == 'simple' && $parentProduct->getId() != $product->getId()){
                            $childProduct = $product;//->setId($product->getParentId());
                            break;
                        }
                    }
                }
            }
        }
        
        if ($childProduct) {
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            $helper->sendMsq($childProduct, 'product_details');
            Mage::register('product', $childProduct);
            Mage::register('current_product', $childProduct);
            $locations = count($product->getCustomerLocations());
            $blockHtml = $this->loadLayout('empty')->getLayout()->getBlock('product.info.stockandprice')->toHtml();
            $result = array('html' => $blockHtml);
        } else {
            $result = array('error' => 'Product not found');
        }
        
        $this->getResponse()->setBody(json_encode($result));
        
    }

}
