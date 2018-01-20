<?php

/**
 * Returns controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_ReturnsController extends Epicor_Customerconnect_Controller_Abstract
{

    public function preDispatch()
    {
        parent::preDispatch();

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        if (!$helper->isReturnsEnabled()) {
            Mage::getSingleton('core/session')->addError($this->__('Returns not available'));
            $this->_redirect('/');
        }
    }

    /**
     * Index action 
     */
    public function indexAction()
    {
        $crrs = Mage::getSingleton('epicor_comm/message_request_crrs');
        /* @var $crrs Epicor_Comm_Model_Message_Request_Crrs */
        $messageTypeCheck = $crrs->getHelper('customerconnect/messaging')->getMessageType('CRRS');

        if ($crrs->isActive() && $messageTypeCheck) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($this->__('ERROR - Returns Search not available'));
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }

    /**
     * Details action 
     */
    public function detailsAction()
    {
        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $success = false;

        Mage::register('review_display', true);
        Mage::register('details_display', true);

        $returnDetails = unserialize($helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('return'))));

        if (isset($returnDetails['return_url'])) {
            Mage::register('return_url', $returnDetails['return']);
            unset($returnDetails['return_url']);
        }

        $erpAccountNumber = $helper->getErpAccountNumber();

        if (
            count($returnDetails) == 2
            && $returnDetails['erp_account'] == $erpAccountNumber
            && !empty($returnDetails['erp_returns_number'])
        ) {

            $return = $helper->loadErpReturn($returnDetails['erp_returns_number']);
            /* @var $return Epicor_Comm_Model_Customer_Return */

            if (!empty($return)) {
                if ($return->canBeAccessedByCustomer()) {
                    Mage::register('return_model', $return);
                    $success = true;
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('Failed to retrieve RFQ Details'));
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('ERROR - Return details not available'));
        }

        if ($success) {
            $this->loadLayout()->renderLayout();
        } else {
            session_write_close();
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Export Return grid to CSV format
     */
    public function exportCsvAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = $baseUrl . '_returns.csv';
        $content = $this->getLayout()->createBlock('customerconnect/customer_returns_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Return grid to XML format
     */
    public function exportXmlAction()
    {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = $baseUrl . '_returns.xml';
        $content = $this->getLayout()->createBlock('customerconnect/customer_returns_list_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

}
