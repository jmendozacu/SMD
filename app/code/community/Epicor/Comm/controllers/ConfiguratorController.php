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

    public function editewaAction()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */
        $this->loadLayout();

        $productId = Mage::app()->getRequest()->getParam('productId');
        $return = Mage::app()->getRequest()->getParam('return');
        $address = Mage::app()->getRequest()->getParam('address');

        $quoteId = Mage::app()->getRequest()->getParam('quoteId');
        $itemId = Mage::app()->getRequest()->getParam('itemId');

        $cimData = array(
            'ewa_code' => Mage::app()->getRequest()->getParam('ewaCode'),
            'group_sequence' => Mage::app()->getRequest()->getParam('groupSequence'),
            'quote_id' => !empty($quoteId) ? $quoteId : null,
            'line_number' => Mage::app()->getRequest()->getParam('lineNumber'),
            'delivery_address' => $helper->getDeliveryAddressFromRFQ($address),
            'item_id' => $itemId
        );

        Mage::register('EWAReturn', $return);
        
        $cim = $helper->sendCim($productId, $cimData);

        if ($cim->isSuccessfulStatusCode()) {
            Mage::register('EWAData', $cim->getResponse()->getConfigurator());
            Mage::register('EWASku', $cim->getProductSku());
            Mage::register('CIMData', new Varien_Object($cimData));
        }

        $this->renderLayout();
    }

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

    public function loadewaAction()
    {
        $helper = Mage::helper('epicor_comm/configurator');
        /* @var $helper Epicor_Comm_Helper_Configurator */
        
        $this->loadLayout();
        
        $productId = Mage::app()->getRequest()->getParam('productId');
        $return = Mage::app()->getRequest()->getParam('return');
        $location = Mage::app()->getRequest()->getParam('location');
        $address = Mage::app()->getRequest()->getParam('address');
        $quoteId = Mage::app()->getRequest()->getParam('quoteId');
        $lineNumber = Mage::app()->getRequest()->getParam('lineNumber');
        
        Mage::register('EWAReturn', $return);
        Mage::register('location_code', $location);
        
        $product = Mage::getModel('catalog/product')->load($productId);
        $cim = Mage::getModel('epicor_comm/message_request_cim');
        /* @var $cim Epicor_Comm_Model_Message_Request_Cim */
        $cim->setProductSku($product->getSku());
        $cim->setProductUom($product->getUom());
        $cim->setQuoteId(!empty($quoteId) ? $quoteId : null);
        $cim->setLineNumber($lineNumber);
        $cim->setDeliveryAddress($helper->getDeliveryAddressFromRFQ($address));
        $cim->sendMessage();
        
        $cimData = array(
            'quote_id' => $cim->getQuoteId(),
            'line_number' => $cim->getLineNumber(),
        );

        if ($cim->isSuccessfulStatusCode())
            Mage::register('EWAData', $cim->getResponse()->getConfigurator());
            Mage::register('EWASku', $product->getSku());
            Mage::register('CIMData', new Varien_Object($cimData));

        $this->renderLayout();
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
}
