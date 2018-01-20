<?php

/**
 * Account controller
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_AccountController extends Epicor_Supplierconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $message = Mage::getSingleton('supplierconnect/message_request_susd');
        /* @var $message Epicor_Supplierconnect_Model_Message_Request_Susd */
        $helper = $message->getHelper("supplierconnect/messaging");
        $messageTypeCheck = $helper->getMessageType('SUSD');

        if ($message->isActive() && $messageTypeCheck) {

            $message->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));


            if ($message->sendMessage()) {
                Mage::register('supplier_connect_account_details', $message->getResponse());
            } else {
                Mage::getSingleton('core/session')->addError('Failed to retrieve Account Details');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Error - Supplier Connect Dashboard not available');
        }

        $this->loadLayout()->renderLayout();
    }

}

