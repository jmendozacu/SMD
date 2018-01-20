<?php

/**
 * Shipments controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_ShipmentsController
        extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $cuss = Mage::getSingleton("customerconnect/message_request_cuss");
        $messageTypeCheck = $cuss->getHelper("customerconnect/messaging")->getMessageType('CUSS');

        if ($cuss->isActive() && $messageTypeCheck) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError("ERROR - Shipment Search not available");
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    public function detailsAction()
    {
        if ($this->_loadShipment()) {
            $this->loadLayout()->renderLayout();
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            session_write_close();
            $this->_redirect('*/*/shipments');
        }
    }

    public function reorderAction()
    {
        $shipment = false;
        if ($this->_loadShipment()) {
            $shipment = Mage::registry('customer_connect_shipments_details');
        }

        $helper = Mage::helper('epicor_common/cart');
        /* @var $helper Epicor_Common_Helper_Cart */

        if (empty($shipment) || !$helper->processReorder($shipment)) {
            if (!Mage::getSingleton('core/session')->getMessages()->getItems()) {
                Mage::getSingleton('core/session')->addError('Failed to build cart for Re-Order request');
            }

            $this->_redirect('checkout/cart/');

            $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('return'));

            $this->_redirectUrl($location);
        } else {
            $this->_redirect('checkout/cart/');
        }
    }

    /**
     * Export Shipments grid to CSV format
     */
    public function exportShipmentsCsvAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_shipments.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_shipments_list_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Shipments grid to XML format
     */
    public function exportShipmentsXmlAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_shipments.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_shipments_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Loads a shipment from the params
     * 
     * return boolean
     */
    private function _loadShipment()
    {
        $loaded = false;
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erpAccountNumber = $helper->getErpAccountNumber();
        $param = Mage::app()->getRequest()->getParam('shipment');
        $shipment = explode(']:[', $helper->decrypt($helper->urlDecode($param)));
        if (
                count($shipment) == 3 &&
                $shipment[0] == $erpAccountNumber &&
                !empty($shipment[1]) &&
                !empty($shipment[2])
        ) {
            $shipment[2] = ($shipment[2] == 'ordernumberempty')? null: $shipment[2];
            $cusd = Mage::getSingleton("customerconnect/message_request_cusd");
            $messageTypeCheck = $cusd->getHelper("customerconnect/messaging")->getMessageType('CUSD');

            if ($cusd->isActive() && $messageTypeCheck) {

                $cusd->setAccountNumber($erpAccountNumber)
                        ->setOrderNumber($shipment[2])
                        ->setShipmentNumber($shipment[1])
                        ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                if ($cusd->sendMessage()) {
                    Mage::register('customer_connect_shipments_details', $cusd->getResults());
                    $loaded = true;
                } else {
                    Mage::getSingleton('core/session')->addError("Failed to retrieve Shipment Details");
                }
            } else {
                Mage::getSingleton('core/session')->addError("ERROR - Shipment Details not available");
            }
        } else {
            Mage::getSingleton('core/session')->addError("ERROR - Invalid Shipment Number");
        }

        return $loaded;
    }

}
