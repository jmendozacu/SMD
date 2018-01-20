<?php

/**
 * Grid controller, handles generic gird functions
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_GridController extends Epicor_Customerconnect_Controller_Abstract {

    /**
     * Clea action - clears the cache for the specified grid
     */
    public function clearAction() {

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        $grid = Mage::app()->getRequest()->getParam('grid');
        $location = Mage::helper('core/url')->urlDecode(Mage::app()->getRequest()->getParam('location'));

        $tags = array('CUSTOMER_' . $customerId . '_CUSTOMERCONNECT_' . strtoupper($grid));
        
        $cache = Mage::app()->getCacheInstance();
        /* @var $cache Mage_Core_Model_Cache */
        $cache->clean($tags);
        
        $this->_redirectUrl($location);
    }
    /***
     * ajax search for page with two grids
     * used in customerconnect/account/index for shipping and contacts 
     */
    public function shippingsearchAction()
    {   
        $this->recreateCUAD();
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('customerconnect/customer_account_shippingaddress_list_grid')->toHtml()    // location of grid block
        );        
    }
    public function contactssearchAction()
    {   
        $this->recreateCUAD();
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('customerconnect/customer_account_contacts_list_grid')->toHtml()    // location of grid block
        );        
    }
    public function orderssearchAction()
    {   
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('customerconnect/customer_dashboard_orders_grid')->toHtml()    // location of grid block
        );        
    }
    public function invoicessearchAction()
    {   
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('customerconnect/customer_dashboard_invoices_grid')->toHtml()    // location of grid block
        );        
    }
    protected function recreateCUAD() 
     {
       
        //resend cuad as registry entry is empty after display. This is copied from the Epicor_Customerconnect_AccountController index (except for last bit) 
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erp_account_number = $helper->getErpAccountNumber();
        $message = Mage::getSingleton('customerconnect/message_request_cuad');
        $error = false;
        $messageTypeCheck = $message->getHelper("customerconnect/messaging")->getMessageType('CUAD');
        if ($message->isActive() && $messageTypeCheck) {
            $message->setAccountNumber($erp_account_number)
                    ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

            if ($message->sendMessage()) {
                Mage::register('customer_connect_account_details', $message->getResults());

                $accessHelper = Mage::helper('epicor_common/access');
                /* @var $helper Epicor_Common_Helper_Access */
                Mage::register('manage_permissions', $accessHelper->customerHasAccess('Epicor_Customerconnect', 'Account', 'index', 'manage_permissions', 'view'));
            } else {
                $error = true;
                Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve Account Details'));
            }
        } else {
            $error = true;
            Mage::getSingleton('core/session')->addError($helper->__('Account Details not available'));
        }
     }  
}

