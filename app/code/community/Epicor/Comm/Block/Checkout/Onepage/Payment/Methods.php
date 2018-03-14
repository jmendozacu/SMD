<?php

class Epicor_Comm_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods {

    public function getQuote()
    {   
        $helper = Mage::helper('customerconnect/arpayments');
        /* @var $helper Epicor_Customerconnect_Helper_Arpayments */          
        if($helper->checkpage()) {
          $quotes = $helper->getArquotes();   
          return $quotes;  
        } else {
          return Mage::getSingleton('checkout/session')->getQuote();  
        }
        
    }
    
//    public function checkPage()
//    {
//        $controller = Mage::app()->getRequest()->getControllerName();
//        $action     = Mage::app()->getRequest()->getActionName();
//        $module     = Mage::app()->getRequest()->getModuleName();
//        if ($module == 'customerconnect' && $controller == 'arpayments') {
//            return true;
//        } else {
//            return false;
//        }
//    }    

}