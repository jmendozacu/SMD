<?php

/**
 * Invoices controller
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_ContractsController
        extends Epicor_Customerconnect_Controller_Abstract
{

    /**
     * Index action 
     */
    public function indexAction()
    {
        $cccs = Mage::getSingleton("customerconnect/message_request_cccs");
        $messageTypeCheck = $cccs->getHelper("customerconnect/messaging")->getMessageType('CCCS');

        if ($cccs->isActive() && $messageTypeCheck) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($this->__("ERROR - Contracts Search not available"));
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                session_write_close();
                $this->_redirect('customer/account/index');
            }
        }
    }
    /**
     * Detail action - show contract details 
     */
    public function detailsAction()
    {
        $contractCode = Mage::app()->getRequest()->getParam('contract');
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erpAccountNumber = $helper->getErpAccountNumber();
        $contractInfo = explode(']:[', $helper->decrypt($helper->urlDecode($contractCode)));
        if (
            count($contractInfo) == 2 &&
            $contractInfo[0] == $erpAccountNumber &&
            !empty($contractInfo[1])
        ) {

            $message = Mage::getSingleton('epicor_lists/message_request_cccd');
            $error = false;
            $messageTypeCheck = $message->getHelper("epicor_list/messaging")->getMessageType('CCCD');
            if ($message->isActive() && $messageTypeCheck) {
                $message->setAccountNumber($erpAccountNumber)
                        ->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()))
                        ->setContractCode($contractInfo[1]);

                if ($message->sendMessage()) {
                    Mage::register('epicor_lists_contracts_details', $message->getResponse());

                    $accessHelper = Mage::helper('epicor_common/access');
                    /* @var $helper Epicor_Common_Helper_Access */
                    Mage::register('manage_permissions', $accessHelper->customerHasAccess('Epicor_Lists', 'Contract', 'index', 'manage_permissions', 'view'));
                } else {
                    $error = true;
                    Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve Customer Contract Details'));
                }
            } else {
                $error = true;
                Mage::getSingleton('core/session')->addError($helper->__('Customer Contract Details not available'));
            }
        } else {
            $error = true;
            Mage::getSingleton('core/session')->addError($helper->__('Customer Contract Id not supplied, cannot display details'));
        }
        $this->loadLayout();
        $this->renderLayout();
    }
   

}
