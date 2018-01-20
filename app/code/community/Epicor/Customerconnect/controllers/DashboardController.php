<?php

/**
 * Customer Dasboard controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_DashboardController extends Epicor_Customerconnect_Controller_Abstract {

    /**
     * Index action 
     */
    public function indexAction() {
        
        $message = Mage::getSingleton('customerconnect/message_request_cuad');
        $helper = $message->getHelper("customerconnect/messaging");
        $messageTypeCheck = $helper->getMessageType('CUAD');
        
        if ($message->isActive() && $messageTypeCheck) {
            Mage::register('customerconnect_dashboard_ok','dashboard ok');
           
             $this->loadLayout();
        }else{          
           Mage::getSingleton('core/session')->addError($helper->__('Error - Customer Connect Dashboard Not Available')); 
           $this->loadLayout()->getLayout()->getBlock('my.account.wrapper')->unsetChild('customer.dashboard.orders');    
           $this->getLayout()->getBlock('my.account.wrapper')->unsetChild('customer.dashboard.invoices');   
        }
        
        session_write_close(); 	
        $this->renderLayout();
    }
     
}