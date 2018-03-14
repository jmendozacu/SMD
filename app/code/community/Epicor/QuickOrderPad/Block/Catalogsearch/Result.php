<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_QuickOrderPad_Block_Catalogsearch_Result extends Mage_CatalogSearch_Block_Result
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        // add Home breadcrumb
        
        $listHelper = Mage::helper('epicor_lists/frontend');
        /* @var $listHelper Epicor_Lists_Helper_Frontend */
        
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        
        $csv = $this->getRequest()->getParam('csv');
        
        $actionName = $this->getRequest()->getActionName();
        'addToCartFromWishlist';
        
        $query = $this->helper('catalogsearch')->getQueryText();
        
        $title = '';
        if ($actionName == 'addToCartFromWishlist') {
            $title = $this->__('Add to cart from Wishlist');
        } elseif ($actionName == 'addToCartFromMyOrdersWidget') {
            $title = $this->__('Add to cart from My Orders Widget');
        } elseif ($csv == 1) {
            $title = $this->__("CSV upload");
        } elseif (!empty($query)) {
            $title = $this->__("Search results for: '%s'", $query);
            if ($list = $listHelper->getSessionList()) {
                $title .= $this->__(' in List: ', $list->getTitle());
            }
        } elseif ($list = $listHelper->getSessionList()) {
            $title = $list->getTitle();
        }
        
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label' => $title,
                'title' => $title
            ));
        }
        
        // modify page title
        $this->getLayout()->getBlock('head')->setTitle($title);

        return $this;
    }
    
    public function getNoResultText()
    {
        $csv = $this->getRequest()->getParam('csv');
        
        if ($csv == 1 || ($this->helper('epicor_lists/frontend')->listsEnabled()
            && !$this->helper('catalogsearch')->getQueryText())) {
            return '';
        } else {
            return parent::getNoResultText();
        }
    }
    
    public function getHeaderText() {
        $listHelper = Mage::helper('epicor_lists/frontend');
        /* @var $listHelper Epicor_Lists_Helper_Frontend */
        
        $csv = $this->getRequest()->getParam('csv');
        
        $queryText = $this->helper('catalogsearch')->getQueryText();
        
        if ($csv == 1) {
            return $this->__("Products that require configuration");
        } elseif (!empty($queryText)) {
            $title = $this->__("Search results for: '%s'", $this->helper('catalogsearch')->getEscapedQueryText());
            if ($list = $listHelper->getSessionList()) {
                $title .= $this->__(' in List: %s', $list->getTitle());
            }
            return $title;
        } elseif ($list = $listHelper->getSessionList()) {
            return $list->getTitle();
        } else {
            return parent::getHeaderText();
        }
    }
}