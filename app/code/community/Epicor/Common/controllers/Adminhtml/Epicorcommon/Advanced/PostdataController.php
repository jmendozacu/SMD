<?php

/**
 * Post data actions
 *
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Adminhtml_Epicorcommon_Advanced_PostdataController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/advanced/data_postdata')
                ->_addBreadcrumb(Mage::helper('epicor_common')->__('Post Data'), Mage::helper('epicor_common')->__('Post Data'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
                ->renderLayout();
    }

    public function postdataAction()
    {
        $xml = '';
        //print_r($this->getRequest()->getPost());die;
        if ($this->getRequest()->getPost('xml')) {
            $xml = $this->getRequest()->getPost('xml');
            $storeId = $this->getRequest()->getParam('post_data_store_id');
            $branding = Mage::helper('epicor_comm')->getStoreBranding($storeId);
            $_company = isset($branding['company']) ? $branding['company'] : null;
            Mage::register('posted_xml_data', $this->getRequest()->getPost(), true);
            $_url = Mage::getStoreConfig('Epicor_Comm/xmlMessaging/url');
            $_api_username = Mage::getStoreConfig('Epicor_Comm/licensing/username');
            $_api_password = Mage::helper('epicor_comm')->decrypt(Mage::getStoreConfig('Epicor_Comm/licensing/password'));
            $connection = new Zend_Http_Client();
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $connection->setUri($_url);
            $adapter->setCurlOption(CURLOPT_HEADER, FALSE);
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, FALSE);
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, FALSE);
            $adapter->setCurlOption(CURLOPT_RETURNTRANSFER, 1);
            $adapter->setCurlOption(CURLOPT_POST, 1);
            $adapter->setCurlOption(CURLOPT_TIMEOUT, 60000);
            $connection->setAdapter($adapter);
            $connection->setHeaders('Content-Length', strlen($xml));
            if (Mage::getStoreConfig('Epicor_Comm/licensing/erp') == 'p21') {
                $connection->setHeaders('Authorization', 'Bearer ' . Mage::getStoreConfig('Epicor_Comm/licensing/p21_token'));
            } else {
                $connection->setAuth($_api_username, $_api_password);
            }
            $callSettings = array(
                'Company' => $_company
            );
            $connection->setHeaders('CallSettings', base64_encode(json_encode($callSettings)));
            $connection->setRawData($xml, 'text/xml');
            $response = $connection->request(Zend_Http_Client::POST);
            $xml_back = $response->getBody();
            $helper = Mage::helper('epicor_common/xml');
            /* @var $helper Epicor_Common_Helper_Xml */

            $valid_xml = simplexml_load_string($xml_back, 'SimpleXmlElement', LIBXML_NOERROR + LIBXML_ERR_FATAL + LIBXML_ERR_NONE);
            if (false == $valid_xml) {
                $this->_getSession()->addError('Message is invalid unble to process');
            } else {
                $resp = "<br/>".$helper->convertXmlToHtml($xml_back);
                $this->_getSession()->addSuccess('XML Message process completed'.$resp);
            }
        }
        $this->_initAction()
                ->renderLayout();
    }

}
