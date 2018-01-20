<?php

/**
 * Parts controller
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */

class Epicor_Supplierconnect_PartsController extends Epicor_Supplierconnect_Controller_Abstract {

    /**
     * Index action 
     */
    public function indexAction() {
        $message = Mage::getSingleton('supplierconnect/message_request_spls');
        $messageTypeCheck = $message->getHelper("supplierconnect/messaging")->getMessageType('SPLS');       
   
        if($message->isActive() && $messageTypeCheck){     
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError('Parts Search not available');
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    public function detailsAction() {
        $helper = Mage::helper('supplierconnect');
        /* @var $helper Epicor_Supplierconnect_Helper_Data */
        $part_requested = unserialize($helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('part'))));
        
        $erp_account_number = Mage::helper('epicor_comm')->getSupplierAccountNumber();
        
        if (
                count($part_requested) == 5 &&
                $part_requested['erp_account'] == $erp_account_number &&
                !empty($part_requested['product_code'])
        ) {
            $spld = Mage::getSingleton('supplierconnect/message_request_spld');
            /* @var $spld Epicor_Supplierconnect_Model_Message_Request_Spld */
            $messageTypeCheck = $spld->getHelper("supplierconnect/messaging")->getMessageType('SPLD');       
   
            if($spld->isActive() && $messageTypeCheck){ 

                $spld->setProductCode($part_requested['product_code'])
                        ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));
                $spld->setOperationalCode($part_requested['operational_code']);
                $spld->setEffectiveDate($part_requested['effective_date']);
                $spld->setUnitOfMeasureCode($part_requested['unit_of_measure_code']);

                if ($spld->sendMessage()) {
                    Mage::register('supplier_connect_part_details', $spld->getResults());
                    $this->loadLayout()->renderLayout();
                } else {
                    Mage::getSingleton('core/session')->addError('Failed to retrieve Part Details');
                }
            } else {
                Mage::getSingleton('core/session')->addError('Part Details not available');
            }
        } else {
            Mage::getSingleton('core/session')->addError('Invalid Part Number');
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }

}

