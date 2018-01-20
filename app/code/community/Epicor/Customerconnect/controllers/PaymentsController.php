<?php

/**
 * Payments controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_PaymentsController extends Epicor_Customerconnect_Controller_Abstract {

    /**
     * Index action 
     */
    public function indexAction() {
        $cups = Mage::getSingleton("customerconnect/message_request_cups");      
        $messageTypeCheck = $cups->getHelper("customerconnect/messaging")->getMessageType('CUPS');        
   
        if($cups->isActive() && $messageTypeCheck){ 
             $this->loadLayout()->renderLayout();
        }else{
             Mage::getSingleton('core/session')->addError("ERROR - Payments Search not available");
              if(Mage::getSingleton('core/session')->getMessages()->getItems()){  
                session_write_close();  
                $this->_redirect('customer/account/index');              
              }
        }     
    }
    /**
     * Export Payments grid to CSV format
     */
    public function exportPaymentsCsvAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_payments.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_payments_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Payments grid to XML format
     */
    public function exportPaymentsXmlAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_payments.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_payments_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }    


}

