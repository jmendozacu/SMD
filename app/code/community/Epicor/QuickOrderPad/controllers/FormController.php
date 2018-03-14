<?php

/**
 * Data controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_QuickOrderPad_FormController extends Mage_Core_Controller_Front_Action
{

    /**
     * Default Page
     */
    public function indexAction()
    {   
        $listHelper = Mage::helper('epicor_lists/frontend');
        /* @var $listHelper Epicor_Lists_Helper_Frontend */
        
        if ($listHelper->listsEnabled() && $listHelper->getSessionList()) {
            $this->_redirect('quickorderpad/form/results');
        } else {
            $quote = Mage::getModel('checkout/cart')->getQuote();
            $_cartQty = Mage::getSingleton('checkout/cart')->getItemsCount();
            if($_cartQty>0){
                    $quote->setTotalsCollectedFlag(false);
                    $quote->collectTotals();
            }
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        }
    }

    /**
     * Results Page
     */
    public function resultsAction()
    {

        foreach ($this->getRequest()->getParams() as $key => $value) {
            if (substr($key, 0, 4) == 'amp;')
                $this->getRequest()->setParam(substr($key, 4), $value);
        }


        $q = $this->getRequest()->getParam('q', '');
        $instock = $this->getRequest()->getParam('instock', '');

        Mage::register('search-query', $q);
//        Mage::register('search-sku', $sku);
        Mage::register('search-instock', $instock != '' ? true : false);
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $_cartQty = Mage::getSingleton('checkout/cart')->getItemsCount();
        if($_cartQty>0){
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
        }
        if ($q != '') {

            $query = Mage::helper('catalogsearch')->getQuery();
            /* @var $query Mage_CatalogSearch_Model_Query */

            $query->setStoreId(Mage::app()->getStore()->getId());
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }

                $query->prepare();
            }

            Mage::helper('catalogsearch')->checkNotes();
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();

            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        } else {
            // remove product from configure products list
            $helper = Mage::helper('epicor_comm/product');
            /* @var $helper Epicor_Comm_Helper_Product */
        
            $listHelper = Mage::helper('epicor_lists/frontend');
            /* @var $listHelper Epicor_Lists_Helper_Frontend */
            
            $csv = $this->getRequest()->getParam('csv');
            if (($csv && $helper->sessionHasConfigureList()) || ($listHelper->listsEnabled() && $listHelper->getSessionList())) {
                $this->loadLayout();
                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('checkout/session');
                $this->renderLayout();
            } else {
                $this->_redirect('quickorderpad/form');
            }
        }
    }
    
    /**
     * Default Page
     */
    public function configclearAction()
    {
        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */

        $helper->clearConfigureList();
        $this->_redirect('quickorderpad/form');
    }
    
    /**
     * Sets the list selected and returns to the results page
     *
     * @return void
     */
    public function listselectAction()
    {
        $listId = $this->getRequest()->getParam('list_id');
        
        $helper = Mage::helper('epicor_lists/frontend');
        /* @var $helper Epicor_Lists_Helper_Frontend */

        if ($helper->getValidListById($listId)) {
            $helper->setSessionList($listId);
        } else {
            $helper->setSessionList(-1);
        }
        
        $this->_redirect('quickorderpad/form/results');
    }

}
