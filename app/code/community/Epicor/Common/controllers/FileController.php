<?php

/**
 * File request controller
 *
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_FileController extends Mage_Core_Controller_Front_Action
{

    /**
     * File Request Action
     */
    public function requestAction()
    {
        $helper = Mage::helper('epicor_common/file');
        /* @var $helper Epicor_Common_Helper_File */
        $file = unserialize($helper->urlDecode(base64_decode(Mage::app()->getRequest()->getParam('file'))));

        //if ($helper->canCustomerAccessFile($file)) {
            try {
                $helper->serveFile($file);
            } catch (Exception $ex) {
                $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
                $this->getResponse()->setHeader('Status', '404 File not found');
                $pageId = Mage::getStoreConfig('web/default/cms_no_route');
                if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
                    $this->_forward('defaultNoRoute');
                }
            }
        //} else {
//            $this->getResponse()->setHeader('HTTP/1.1', '400 Access Denied');
//            $this->getResponse()->setHeader('Status', '400 Access Denied');
//            $pageId = Mage::getStoreConfig('web/default/cms_no_route');
//            if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
//                $this->_forward('defaultNoRoute');
//            }
//        }
    }

}
