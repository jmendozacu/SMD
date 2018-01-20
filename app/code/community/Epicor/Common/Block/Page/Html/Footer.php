<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Common_Block_Page_Html_Footer extends Mage_Page_Block_Html_Footer
{
    public function getCacheKeyInfo()
    {
        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        
        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */
        
        $extraKey = $customer->isSupplier() ? 'supplier' : ($customer->isSupplier() ? 'customer' : ($customer->isSalesRep() ? 'salesrep' : 'guest'));
        
        $keyInfo = array(
            'PAGE_FOOTER',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->isLoggedIn(),
            $extraKey
        );
        
        return $keyInfo;
    }
}