<?php

/**
 * EWA Configurator Controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_ConfiguratorController extends Mage_Core_Controller_Front_Action
{

    public function badurlAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function errorAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function reorderewaAction()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */

        $productId = Mage::app()->getRequest()->getParam('productId');
        $groupSequence = Mage::app()->getRequest()->getParam('groupSequence');

        $helper->reorderProduct($productId, $groupSequence);

        $this->_redirect('checkout/cart');
    }

    public function ewacssAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/css', true);
        echo Mage::getStoreConfig('epicor_comm_enabled_messages/cim_request/ewa_css');
    }

    public function ewacompleteAction()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */

        $ewaCode = $helper->urlDecode(Mage::app()->getRequest()->getParam('EWACode'));
        $productSku = $helper->urlDecode(Mage::app()->getRequest()->getParam('SKU'));
        $locationCode = $helper->urlDecode(Mage::app()->getRequest()->getParam('location'));
        $itemId = $helper->urlDecode(Mage::app()->getRequest()->getParam('itemId'));
        $qty = $helper->urlDecode(Mage::app()->getRequest()->getParam('qty'));
        $qty = $qty ? $qty : 1;
        $url = $helper->addProductToBasket($productSku, $ewaCode, false, $qty, $locationCode, $itemId);
//        $url = $helper->addProductToBasket($productSku, $ewaCode, false, 1, $locationCode);

        echo '
                <script type="text/javascript" src="' . Mage::getBaseUrl('js') . 'prototype/prototype.js"></script>
                <script type="text/javascript">
                //<![CDATA[ 
                        $(parent).ewaProduct.redirect("' . $url . '");
                //]]>
                    </script>
                    ';
    }
    /*
     * This is executed from the quickadd block and checks if the product is a configurator
     */
    public function configuratorcheckAction()
    {
        $productId = $this->getRequest()->getParam('productId');
        if(!$productId){            
            $productSku = $this->getRequest()->getParam('sku');

            //get the product id of the sku supplied
            $product = Mage::helper('epicor_comm')->findProductBySku($productSku);
            $productId = $product ? $product->getId() : ''; 
        }
       
        $configurator = $productId ? Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'configurator', Mage::app()->getStore()) : 0;
        
        //$configurator will only contain an array(empty) if field has never been set, otherwise it will contain 0 or 1
        //ensure value returned is not an array or string 
        $configurator = is_array($configurator)? 0 : (int)$configurator;
        $this->getResponse()->setBody(json_encode(array('configurator' => $configurator,'productId'=>$productId, 'type' => 'success'))); 
    }
}
