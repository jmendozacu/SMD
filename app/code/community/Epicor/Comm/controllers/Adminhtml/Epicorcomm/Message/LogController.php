<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LogController
 *
 * @author David.Wylie
 */
class Epicor_Comm_Adminhtml_Epicorcomm_Message_LogController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected $_returns = array('CREU','CRRC');
     
    protected $_aclId = 'epicor_common/messaging/log';

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/messaging/log')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Messaging Log'), Mage::helper('adminhtml')->__('Messaging Log'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Messaging Log'));
        $this->_initAction()
                ->renderLayout();
    }

    public function exportCsvAction()
    {
        $fileName = 'messages.csv';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_message_log_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'messages.xml';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_message_log_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function cleanAction()
    {
        $log = Mage::getModel('epicor_comm/message_log');
        $log->clean();
        Mage::getSingleton('core/session')->addSuccess('Log Cleared');
    }

    private function getBackRoute()
    {
        $source = $this->getRequest()->getParam('source');

        switch ($source) {
            case 'customer': $route = 'adminhtml/epicorcomm_customer_erpaccount/edit';
                break;
            case 'product' : $route = 'adminhtml/catalog_product/edit';
                break;
            case 'order' : $route = 'adminhtml/sales_order/view';
                break;
            case 'return' : $route = 'adminhtml/epicorcomm_returns/view';
                break;
            case 'notification' : $route = 'adminhtml/notification';
                break;
            case 'list' : $route = 'adminhtml/epicorlists_list/edit';
                break;
            default: $route = '*/*';
        }

        return $route;
    }

    private function getSourceParams()
    {
        $sourceIdParam = $this->getRequest()->getParam('sourceid', null);
        if (!empty($sourceIdParam)) {
            $source = $this->getRequest()->getParam('source');
            if ($source == 'order') {
                $sourceId = array('order_id' => $sourceIdParam);
            } else {
                $sourceId = array('id' => $sourceIdParam);
            }
        } else {
            $sourceId = null;
        }

        return $sourceId;
    }

    public function viewAction()
    {

        $this->_title($this->__('Message Log Details'));
        $source = $this->getBackRoute();
        $sourceId = $this->getSourceParams();
        Mage::register('message_log_source', $source);

        Mage::register('message_log_sourceparam', $sourceId);
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('epicor_comm/message_log');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Log entry missing'));
                $this->_redirect($source, $sourceId);
            }
        }

        Mage::register('message_log_data', $model);

        $date = Mage::helper('epicor_comm')->getLocalDate($model->getStartDatestamp());

        $header = 'Log entry for ' . $model->getMessageType() . ' at ' . $date;
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->getLayout()->getBlock('erp_header')->setHeaderText($header);
        $this->renderLayout();
    }

    public function reprocessAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->reprocess($id);
        $source = $this->getBackRoute();
        $sourceId = $this->getSourceParams();
        $this->_redirect($source, $sourceId);
    }

    public function massReprocessAction()
    {
        $ids = (array) $this->getRequest()->getParam('logid');
        foreach ($ids as $id) {
            $this->reprocess($id);
        }
        $this->_redirect('*/*/');
    }

    private function reprocess($id)
    {
        $model = Mage::getModel('epicor_comm/message_log');
        $session = Mage::getSingleton('adminhtml/session');
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $rawType = $model->getMessageType();
                $type = strtolower($rawType);
                $note = "$rawType for '" . $model->getMessageSubject() . "'";
                if ($model->getMessageParent() != 'Upload') {
                    $session->addWarning($helper->__("$note is not an Upload Message so cannot be Reprocessed"));
                } else {
                    $model->setMessageStatus(Epicor_Comm_Model_Message_Log::MESSAGE_STATUS_REPROCESSED);
                    $model->save();

                    $response = $helper->processSingleMessage($model->getXmlIn());
                    if ($response->getIsSuccessful()) {
                        $session->addSuccess($helper->__("Re-processed $note successfully see log entry for details"));
                    } else {
                        $session->addError($helper->__("Re-processing $note failed see log entry for details"));
                    }
                }
            } else {
                $session->addError($helper->__('Log entry missing'));
            }
        }
    }

    public function gridAction()
    {

        $source = $this->getRequest()->getParam('source');
        $sourceId = $this->getSourceParams();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('epicor_comm/adminhtml_catalog_product_edit_tab_log')
                ->setUseAjax(true);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->delete($id);
        $source = $this->getBackRoute();
        $sourceId = $this->getSourceParams();
        $this->_redirect($source, $sourceId);
    }

    public function massDeleteAction()
    {
        $ids = (array) $this->getRequest()->getParam('logid');
        foreach ($ids as $id) {
            $this->delete($id, true);
        }
        $helper = Mage::helper('epicor_comm');
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($helper->__(count($ids) . ' Message log entries deleted'));
        $this->_redirect('*/*/');
    }

    private function delete($id, $mass = false)
    {
        $model = Mage::getModel('epicor_comm/message_log');
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $helper Epicor_Comm_Helper_Data */
        $helper = Mage::helper('epicor_comm');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {

                if ($model->delete()) {
                    if (!$mass) {
                        $session->addSuccess($helper->__('Message log entry deleted'));
                    }
                } else {
                    $session->addError($helper->__('Could not delete Message Log ' . $id));
                }
            }
        }
    }

}
