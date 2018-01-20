<?php

/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */
class Blackbird_ContentManager_Model_Router extends Mage_Core_Controller_Varien_Router_Abstract {

    public function initControllerRouters($observer) {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('contenttype_router', $this);
    }

    /**
     * Validate and Match router with the Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request) {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                    ->setRedirect(Mage::getUrl('install'))
                    ->sendResponse();
            exit;
        }

        //identifier in the URL
        $identifier = trim($request->getPathInfo(), '/');

        //dispatch a "match before" event
        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue' => true
        ));

        Mage::dispatchEvent('contenttype_controller_router_match_before', array(
            'router' => $this,
            'condition' => $condition
        ));

        //refresh $identifier variable in case the identifier was modified by an event
        $identifier = $condition->getIdentifier();

        //redirect url setted by event
        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                    ->setRedirect($condition->getRedirectUrl())
                    ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        //stop router
        if (!$condition->getContinue()) {
            return false;
        }

        //get content by his URL
        $result1 = $this->checkContentExists($identifier, $request);

        //get contentlist by his URL
        $result2 = $this->checkContentListExists($identifier, $request);
        if (!$result1 && !$result2) {
            return false;
        }
        $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier
        );

        return true;
    }

    /**
     * Add location header and disable browser page caching
     *
     * @param string $url
     * @param bool $isPermanent
     */
    protected function _sendRedirectHeaders($url, $isPermanent = false) {
        if ($isPermanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Location: ' . $url);
        exit;
    }

    public function checkContentExists($identifier, $request) {
        $content = Mage::getModel('contentmanager/content')
                ->getCollection()
                ->addAttributeToFilter('url_key', $identifier);

        if (Mage::app()->getRequest()->getParam('preview') != 1) {
            $content->addAttributeToFilter('status', 1);
        }

        if (!$content->getSize()) {
            try {

                $fromStoreId = Mage::app()->getStore($request->getParam('___from_store'))->getId();

                //test if redirection from another store
                if ($fromStoreId > 0) {
                    //load previous content
                    $contentPrevious = Mage::getModel('contentmanager/content')
                            ->getCollection()
                            ->addStoreFilter($fromStoreId)
                            ->addAttributeToFilter('url_key', $identifier);

                    if (Mage::app()->getRequest()->getParam('preview') != 1) {
                        $contentPrevious->addAttributeToFilter('status', 1);
                    }

                    if ($contentPrevious->getSize() > 0 && ($contentPrevious->getFirstItem()->existsForStore(Mage::app()->getStore()->getId()) || $contentPrevious->getFirstItem()->existsForStore(0))) {
                        //load corresponding content
                        $contentCorresponding = Mage::getModel('contentmanager/content')
                                ->getCollection()
                                ->addAttributeToSelect('url_key')
                                ->addAttributeToFilter('entity_id', $contentPrevious->getFirstItem()->getId());

                        if (Mage::app()->getRequest()->getParam('preview') != 1) {
                            $contentCorresponding->addAttributeToFilter('status', 1);
                        }

                        $this->_sendRedirectHeaders($contentCorresponding->getFirstItem()->getUrl(), false);
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }

            //get content by his menu URL
            $nodes = Mage::getModel('contentmanager/menu_node')
                    ->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('type', 'content')
                    ->addFieldToFilter('url_path', $identifier);

            if ($nodes->getSize() > 0) {
                $content = Mage::getModel('contentmanager/content')
                        ->getCollection()
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToFilter('entity_id', $nodes->getFirstItem()->getEntityId());

                if (!$content->getSize()) {
                    return false;
                }
            } else {
                return false;
            }
        }

        //redirect to corresponding controller
        $request->setModuleName('contenttype')
                ->setControllerName('index')
                ->setActionName('view')
                ->setParam('content_id', $content->getFirstItem()->getId());
        return true;
    }

    public function checkContentListExists($identifier, $request) {
        
        $contentList = Mage::getModel('contentmanager/contentlist')
                ->getCollection()
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToFilter('url_key', $identifier);

        if (Mage::app()->getRequest()->getParam('preview') != 1) {
            $contentList->addFieldToFilter('status', 1);
        }
        if (!$contentList->getSize()) {
            return false;
        }

        //redirect to corresponding controller
        $request->setModuleName('contenttype')
                ->setControllerName('index')
                ->setActionName('list')
                ->setParam('contentlist_id', $contentList->getFirstItem()->getId());

        return true;
    }

}
