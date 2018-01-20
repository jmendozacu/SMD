<?php

/**
 * RMAs controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_RmasController extends Epicor_Customerconnect_Controller_Abstract {

    /**
     * Index action 
     */
    /**
     * Index action 
     */
    public function indexAction() {
        $curs = Mage::getSingleton("customerconnect/message_request_curs");      
        $messageTypeCheck = $curs->getHelper("customerconnect/messaging")->getMessageType('CURS');        
   
        if($curs->isActive() && $messageTypeCheck){ 
             $this->loadLayout()->renderLayout();
        }else{
             Mage::getSingleton('core/session')->addError("ERROR - RMA Search not available");
              if(Mage::getSingleton('core/session')->getMessages()->getItems()){  
                session_write_close();  
                $this->_redirect('customer/account/index');              
              }
        }     
    }
    /**
     * Export  Rmas grid to CSV format
     */
    public function exportRmasCsvAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_rmas.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_rmas_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Rmas grid to XML format
     */
    public function exportRmasXmlAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_rmas.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_rmas_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }    


}

