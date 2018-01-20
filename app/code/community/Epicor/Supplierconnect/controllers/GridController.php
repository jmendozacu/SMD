<?php

/**
 * Grid controller, handles generic gird functions
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */

class Epicor_Supplierconnect_GridController extends Epicor_Supplierconnect_Controller_Abstract {

    /**
     * Clea action - clears the cache for the specified grid
     */
    public function clearAction() {

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        $grid = Mage::app()->getRequest()->getParam('grid');
        $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('location'));

        $tags = array('CUSTOMER_' . $customerId . '_SUPPLIERCONNECT_' . strtoupper($grid));
        
        $cache = Mage::app()->getCacheInstance();
        /* @var $cache Mage_Core_Model_Cache */
        $cache->clean($tags);
        
        $this->_redirectUrl($location);
    }

}

