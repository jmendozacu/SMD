<?php

/**
 * Display controller
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_RfqController extends Epicor_Supplierconnect_Controller_Abstract
{

    public function indexAction()
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        $surs = Mage::getSingleton('supplierconnect/message_request_surs');
        /* @var $surs Epicor_Supplierconnect_Model_Message_Request_Surs */
        if ($surs->isActive() && $helper->getMessageType('SURS')) {
            $this->loadLayout()->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($helper->__('RFQ Search not available'));
            if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
                $this->_redirect('customer/account/index');
            }
        }
    }

    public function detailsAction()
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $rfq_requested = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('rfq'))));

        $erp_account_number = Mage::helper('epicor_comm')->getSupplierAccountNumber();

        if (
                count($rfq_requested) == 3 &&
                $rfq_requested[0] == $erp_account_number &&
                !empty($rfq_requested[1]) &&
                !empty($rfq_requested[2])
        ) {
            $surd = Mage::getSingleton('supplierconnect/message_request_surd');
            /* @var $surd Epicor_Supplierconnect_Model_Message_Request_Surd */

            if ($surd->isActive() && $helper->getMessageType('SURD')) {
                $surd->setRfqNumber($rfq_requested[1]);
                $surd->setLine($rfq_requested[2]);
                $surd->setLanguageCode($helper->getLanguageMapping(Mage::app()->getLocale()->getLocaleCode()));

                if ($surd->sendMessage()) {

                    $rfqMsg = $surd->getResults();

                    if ($rfqMsg) {

                        $rfq = $rfqMsg->getRfq();

                        if ($rfq) {

                            Mage::register('supplier_connect_rfq_details', $rfq);
                            $allowOverride = ($rfq->getAllowConversionOverride() == 'Y') ? true : false;
                            Mage::register('allow_conversion_override', $allowOverride);

                            $accessHelper = Mage::helper('epicor_common/access');
                            /* @var $helper Epicor_Common_Helper_Access */

                            $editable = $accessHelper->customerHasAccess('Epicor_Supplierconnect', 'Rfq', 'update', '', 'Access');

                            if ($rfq->getStatus() == 'C') {
                                $editable = false;
                            }

                            Mage::register('rfq_editable', $editable);

                            $this->loadLayout()->renderLayout();
                        } else {
                            Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve RFQ Details from message'));
                        }
                    } else {
                        Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve RFQ Details from message'));
                    }
                } else {
                    Mage::getSingleton('core/session')->addError($helper->__('Failed to retrieve RFQ Details'));
                }
            } else {
                Mage::getSingleton('core/session')->addError($helper->__('RFQ Details not available'));
            }
        } else {
            Mage::getSingleton('core/session')->addError($helper->__('Invalid RFQ Number'));
        }

        if (Mage::getSingleton('core/session')->getMessages()->getItems()) {
            $this->_redirect('*/*/index');
        }
    }

    public function updateAction()
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        if ($newData = $this->getRequest()->getPost()) {
            $oldData = unserialize(base64_decode($newData['old_data']));
            unset($newData['old_data']);

            $suru = Mage::getSingleton('supplierconnect/message_request_suru');
            /* @var $suru Epicor_Supplierconnect_Model_Message_Request_Suru */

            if ($suru->isActive() && $helper->getMessageType('SURU')) {
                $suru->setRfqNumber($oldData['rfq_number']);
                $suru->setLine($oldData['line']);
                $suru->setOldData($oldData);
                $suru->setNewData($newData);

                if ($suru->sendMessage()) {
                    Mage::getSingleton('core/session')->addSuccess($helper->__('RFQ update request sent successfully'));
                } else {
                    $error = $helper->__('RFQ update request failed : %s', $suru->getStatusDescription());
                }
            } else {
                $error = $helper->__('RFQ update not avaithelable');
            }
        } else {
            $error = $helper->__('No Data Sent');
        }

        if ($error) {
            echo json_encode(array('message' => $error, 'type' => 'error'));
        } else {
            $rfq_requested = $helper->urlEncode($helper->encrypt($helper->getSupplierAccountNumber() . ']:[' . $oldData['rfq_number'] . ']:[' . $oldData['line']));
            $url = Mage::getUrl('*/*/details', array('rfq' => $rfq_requested));
            echo json_encode(array('redirect' => $url, 'type' => 'success'));
        }
    }

}
