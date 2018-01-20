<?php

/**
 * Recent Purchases controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_RecentPurchasesController extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $cphs = Mage::getSingleton("customerconnect/message_request_cphs");
        $messageTypeCheck = $cphs->getHelper("customerconnect/messaging")->getMessageType('CPHS');

        if (true) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($this->__("ERROR - Recent Purchases search not available"));
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    
    /**
     * Export RecentPurchase grid to CSV format
     */
    public function exportOrdersCsvAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_recentpurchases.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_recentpurchases_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export RecentPurchase grid to XML format
     */
    public function exportOrdersXmlAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_recentpurchases.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_recentpurchases_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}
