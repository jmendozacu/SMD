<?php

/**
 * Payments controller
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_PaymentsController extends Epicor_Supplierconnect_Controller_Abstract {

    /**
     * Index action - purchase order list page
     */
    public function indexAction() {
        $message = Mage::getSingleton('supplierconnect/message_request_sups');
        $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SUPS');       
   
        if($message->isActive() && $messageTypeCheck){     
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('Payment Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('customer/account/index');
            }
        }
    }
}