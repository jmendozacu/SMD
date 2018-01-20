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
class Blackbird_ContentManager_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Renders Content Page
     *
     * @param string $coreRoute
     */
    public function viewAction() {
        //load content
        $contentId = $this->getRequest()->getParam('content_id', $this->getRequest()->getParam('id', false));
        $content = Mage::getModel('contentmanager/content')->load($contentId);
        Mage::register('current_content', $content);

        //load cct
        $cct = Mage::getModel('contentmanager/contenttype')->load($content->getCtId());
        Mage::register('current_ct', $cct);

        //load layout & update
        Mage::dispatchEvent('cct_content_render', array('content' => $content, 'cct' => $cct, 'controller_action' => $this));

        //Content type dynamic layout handler
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();
        $update->addHandle('CONTENT_TYPE_VIEW_' . $cct->getIdentifier());

        //load layout
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $layoutUpdate = $cct->getLayoutUpdateXml();
        $this->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;

        //Layout cache ID
        $update->setCacheId('LAYOUT_' . Mage::app()->getStore()->getId() . md5(join('__', $update->getHandles())));

        //add body class
        $root = $this->getlayout()->getBlock('root');
        if ($root) {
            $root->addBodyClass('contentmanager-contenttype-' . $cct->getIdentifier());
            $root->addBodyClass('contentmanager-content-' . $contentId);
        }

        //apply root template update
        if ($cct->getRootTemplate()) {
            $this->getLayout()->helper('page/layout')
                    ->applyTemplate($cct->getRootTemplate());
        }

        //apply breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if($breadcrumbs && $cct->getBreadcrumb())
        {
            $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            $storeId = Mage::app()->getStore()->getId();

            if($cct->getBreadcrumbPrevName() && $cct->getBreadcrumbPrevLink())
            {
                $breadcrumbPrevName = unserialize($cct->getBreadcrumbPrevName());
                $breadcrumbPrevLink = unserialize($cct->getBreadcrumbPrevLink());

                if($breadcrumbPrevName[$storeId])
                {
                    $breadcrumbs->addCrumb('cct_content_prev', array('label'=>$breadcrumbPrevName[$storeId], 'title'=>$breadcrumbPrevName[$storeId], 'link' => $breadcrumbPrevLink[$storeId]));
                }
            }
            elseif($cct->getBreadcrumbPrevName() && !$cct->getBreadcrumbPrevLink())
            {
                $breadcrumbPrevName = unserialize($cct->getBreadcrumbPrevName());
                if($breadcrumbPrevName[$storeId])
                {
                    $breadcrumbs->addCrumb('cct_content_prev', array('label'=>$breadcrumbPrevName[$storeId], 'title'=>$breadcrumbPrevName[$storeId]));
                }
            }

            if ($cct->getBreadcrumb())
            {
                $breadcrumbs->addCrumb('cct_content', array('label'=>$content->getData($cct->getBreadcrumb()), 'title'=>$content->getData($cct->getBreadcrumb())));
            }
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $this->getLayout()->getMessagesBlock();
        foreach (array('catalog/session', 'checkout/session', 'customer/session') as $storageType) {
            $storage = Mage::getSingleton($storageType);
            if ($storage) {
                if (version_compare(Mage::getVersion(), '1.6', '>=')) {
                    //version is 1.6 or greater   
                    $messageBlock->addStorageType($storageType);
                }
                $messageBlock->addMessages($storage->getMessages(true));
            }
        }

        //set page title
        $this->_setTitleMetas();

        //render page
        $this->renderLayout();
    }

    /**
     * Renders Content List
     *
     * @param string $coreRoute
     */
    public function listAction() {
        //load content list
        $contentId = $this->getRequest()->getParam('contentlist_id', $this->getRequest()->getParam('id', false));
        $contentList = Mage::getModel('contentmanager/contentlist')->load($contentId);
        Mage::register('current_contentlist', $contentList);

        //load cct
        $cct = Mage::getModel('contentmanager/contenttype')->load($contentList->getCtId());
        Mage::register('current_ct', $cct);

        //load layout & update
        Mage::dispatchEvent('cct_contentlist_render', array('contentList' => $contentList, 'cct' => $cct, 'controller_action' => $this));

        //Content type dynamic layout handler
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();
        $update->addHandle('CONTENTLIST_VIEW_' . $cct->getIdentifier());

        //load layout
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $layoutUpdate = $contentList->getLayoutUpdateXml();
        $this->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;

        //Layout cache ID
        $update->setCacheId('LAYOUT_' . Mage::app()->getStore()->getId() . md5(join('__', $update->getHandles())));

        //add body class
        $root = $this->getlayout()->getBlock('root');
        if ($root) {
            $root->addBodyClass('contentmanager-contentlist-contenttype-' . $cct->getIdentifier());
            $root->addBodyClass('contentmanager-contentlist-' . $contentId);
        }

        //apply root template update
        if ($contentList->getRootTemplate()) {
            $this->getLayout()->helper('page/layout')
                    ->applyTemplate($contentList->getRootTemplate());
        }

        //apply breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs && $contentList->getBreadcrumb()) {
            $breadcrumbs->addCrumb('home', array('label' => Mage::helper('cms')->__('Home'), 'title' => Mage::helper('cms')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));
            $storeId = Mage::app()->getStore()->getId();

            if ($contentList->getBreadcrumbPrevName() && $contentList->getBreadcrumbPrevLink()) {
                $breadcrumbPrevName = unserialize($contentList->getBreadcrumbPrevName());
                $breadcrumbPrevLink = unserialize($contentList->getBreadcrumbPrevLink());

                if($breadcrumbPrevName[$storeId])
                {
                    $breadcrumbs->addCrumb('cct_content_prev', array('label' => $breadcrumbPrevName[$storeId], 'title' => $breadcrumbPrevName[$storeId], 'link' => $breadcrumbPrevLink[$storeId]));
                }
            } elseif ($contentList->getBreadcrumbPrevName() && !$contentList->getBreadcrumbPrevLink()) {
                $breadcrumbPrevName = unserialize($contentList->getBreadcrumbPrevName());
                if($breadcrumbPrevName[$storeId])
                {
                    $breadcrumbs->addCrumb('cct_content_prev', array('label' => $breadcrumbPrevName[$storeId], 'title' => $breadcrumbPrevName[$storeId]));
                }
            }

            if ($contentList->getBreadcrumb()) {
                $breadcrumbs->addCrumb('cct_content', array('label' => $contentList->getData($contentList->getBreadcrumb()), 'title' => $contentList->getData($contentList->getBreadcrumb())));
            }
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $this->getLayout()->getMessagesBlock();
        foreach (array('catalog/session', 'checkout/session', 'customer/session') as $storageType) {
            $storage = Mage::getSingleton($storageType);
            if ($storage) {
                if (version_compare(Mage::getVersion(), '1.6', '>=')) {
                    //version is 1.6 or greater   
                    $messageBlock->addStorageType($storageType);
                }
                $messageBlock->addMessages($storage->getMessages(true));
            }
        }

        //set page title
        $this->_setListTitleMetas();

        //render page
        $this->renderLayout();
    }

    public function _setTitleMetas() {
        $content = Mage::registry('current_content');

        $head = $this->getLayout()->getBlock('head');

        if ($head) {
            //add title and meta tags
            $head->setTitle($content->getMetaTitle()?$content->getMetaTitle():$content->getTitle());
            $head->setKeywords($content->getKeywords());
            $head->setDescription($content->getDescription());
            $head->setRobots($content->getRobots());

            //add canonical url
            $cct = Mage::registry('current_ct');

            if ($cct && $cct->getUrlMenu() == 1) {
                //get content by his menu URL
                $nodes = Mage::getModel('contentmanager/menu_node')
                        ->getCollection()
                        ->addFieldToFilter('type', 'content')
                        ->addFieldToFilter('url_path', array('neq' => ''))
                        ->addFieldToFilter('canonical', 1)
                        ->addFieldToFilter('entity_id', $content->getId());

                if (Mage::app()->getRequest()->getParam('preview') != 1) {
                    $nodes->addFieldToFilter('status', 1);
                }

                if ($nodes->getSize() > 0) {
                    $canonicalUrl = Mage::getUrl($nodes->getFirstItem()->getUrlPath(), array('_direct' => $nodes->getFirstItem()->getUrlPath()));
                } else {
                    $canonicalUrl = $content->getUrl();
                }

                $head->addLinkRel('canonical', $canonicalUrl);
            }
        }

        //add open graph block
        $block_og = $this->getLayout()->createBlock('contentmanager/og');
        $block_og->setData('og_title', $content->getOgTitle());
        $block_og->setData('og_description', $content->getOgDescription());
        $block_og->setData('og_url', $content->getOgUrl());
        $block_og->setData('og_type', $content->getOgType());
        $block_og->setData('og_image', $content->getOgImage());

        $head->append($block_og, 'og_tags');
    }

    public function _setListTitleMetas() {
        $contentList = Mage::registry('current_contentlist');
        $head = $this->getLayout()->getBlock('head');

        if ($head) {

            //add title and meta tags
            if ($contentList->getMetaTitle()) {
                $head->setTitle($contentList->getMetaTitle());
            }
            else{
                $head->setTitle($contentList->getTitle());
            }
            $head->setKeywords($contentList->getMetaKeywords());
            $head->setDescription($contentList->getMetaDescription());
            $head->setRobots($contentList->getMetaRobots());
        }

        //add open graph block
        $block_og = $this->getLayout()->createBlock('contentmanager/og');
        $block_og->setData('og_title', $contentList->getOgTitle());
        $block_og->setData('og_description', $contentList->getOgDescription());
        $block_og->setData('og_url', $contentList->getOgUrl());
        $block_og->setData('og_type', $contentList->getOgType());
        $block_og->setData('og_image', $contentList->getOgImage());

        $head->append($block_og, 'og_tags');
    }

}
