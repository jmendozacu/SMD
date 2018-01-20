<?php

/**
 * Orders controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_OrdersController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $cuos = Mage::getSingleton("customerconnect/message_request_cuos");
        $messageTypeCheck = $cuos->getHelper("customerconnect/messaging")->getMessageType('CUOS');

        if ($cuos->isActive() && $messageTypeCheck) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($this->__("ERROR - Order Search not available"));
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    public function detailsAction()
    {
        if($this->_getOrderDetails()) {
            $this->loadLayout()->renderLayout();
        }
        
        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            $this->_redirect('*/*/index');
        }
    }

    public function reorderAction()
    {
        $order = false;
        if($this->_getOrderDetails()) {
            $order = Mage::registry('customer_connect_order_details');
        }
        
        $helper = Mage::helper('epicor_common/cart');
        /* @var $helper Epicor_Common_Helper_Cart */

        if (empty($order) || !$helper->processReorder($order)) {
            if (!Mage::getSingleton('core/session')->getMessages()->getItems()) {
                Mage::getSingleton('core/session')->addError($this->__('Failed to build cart for Re-Order request'));
            }

            $this->_redirect('checkout/cart/');

            $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('return'));

            $this->_redirectUrl($location);
        } else {
            $this->_redirect('checkout/cart/');
        }
    }

    /**
     * Performs a CUOD request
     * 
     * @return boolean
     */
    private function _getOrderDetails()
    {
        $orderId = Mage::app()->getRequest()->getParam('order');
        $order = false;
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erpAccountNumber = $helper->getErpAccountNumber();
        $orderInfo = explode(']:[', $helper->decrypt($helper->urlDecode($orderId)));
        if (
                count($orderInfo) == 2 &&
                $orderInfo[0] == $erpAccountNumber &&
                !empty($orderInfo[1])
        ) {

            $result = $helper->sendOrderRequest($erpAccountNumber, $orderInfo[1], $helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

            if ($result['order']) {
                Mage::register('customer_connect_order_details', $result['order']);
                $order = true;
            } else {
                Mage::getSingleton('core/session')->addError($this->__($result['error']));
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('ERROR - Invalid Order Number'));
        }

        return $order;
    }
    /**
     * Export Orders grid to CSV format
     */
    public function exportOrdersCsvAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_orders.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_orders_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Orders grid to XML format
     */
    public function exportOrdersXmlAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_orders.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_orders_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}
