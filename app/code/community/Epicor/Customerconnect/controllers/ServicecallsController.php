<?php

/**
 * Service Calls controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_ServicecallsController extends Epicor_Customerconnect_Controller_Abstract {

    /**
     * Index action 
     */
    public function indexAction() {
        $cucs = Mage::getSingleton("customerconnect/message_request_cucs");      
        $messageTypeCheck = $cucs->getHelper("customerconnect/messaging")->getMessageType('CUCS');        
   
        if($cucs->isActive() && $messageTypeCheck){ 
             $this->loadLayout()->renderLayout();
        }else{
             Mage::getSingleton('core/session')->addError("ERROR - Service Calls Search not available");
              if(Mage::getSingleton('core/session')->getMessages()->getItems()){  
                session_write_close();  
                $this->_redirect('customer/account/index');              
              }
        }     
    }
    /**
     * Export Servicecalls grid to CSV format
     */
    public function exportServicecallsCsvAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_servicecalls.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_servicecalls_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Servicecalls grid to XML format
     */
    public function exportServicecallsXmlAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_servicecalls.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_servicecalls_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }    


}

