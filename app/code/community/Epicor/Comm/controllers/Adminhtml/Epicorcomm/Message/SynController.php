<?php

/**
 * Epicor_Comm_Adminhtml_Message_SynController
 * 
 * Controller for Epicor > Messages > Send SYN
 * 
 * @author Gareth.James
 */
class Epicor_Comm_Adminhtml_Epicorcomm_Message_SynController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/messaging/commsync')
                ->_addBreadcrumb(Mage::helper('epicor_comm')->__('Send SYN request'), Mage::helper('epicor_comm')->__('Send SYN request'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }
    
    public function logAction() {
        $this->_title($this->__('SYN Log'));
        $this->_initAction()
                ->renderLayout();
    }
    
    public function deletelogAction()
    {
        $ids = (array) $this->getRequest()->getParam('logid');
        $count = count($ids);
        $session = Mage::getSingleton('adminhtml/session');

        foreach ($ids as $id) {
            $model = Mage::getModel('epicor_comm/syn_log');
            /* @var $model Epicor_Comm_Model_Syn_Log */
            $model->load($id);
            if ($model->getId()) {
                if (!$model->delete()) {
                    $session->addError($this->__('Could not delete SYN Log ' . $id));
                    $count--;
                }
            }
        }

        $helper = Mage::helper('epicor_comm');
        $session->addSuccess($this->__('%s SYN log entries deleted', $count));

        $this->_redirect('*/*/log');
    }

    public function sendAction() {
        if ($data = $this->getRequest()->getPost()) {

            if(array_key_exists('advanced_messages', $data)){       // if advanced messages exist the advanced option has been selected - ignore simple  
                $messages = $data['advanced_messages'];
            }else{
                $messages = explode(',',implode(',', $data['simple_messages'])); // implode multimessage elements to string then explode string to array of single messages
            }
            
            $messageWeighting =  Mage::helper('epicor_comm/messaging')->getMessageTypeWeighting();  
            // order selected messages according to weighting
            $sortedMessageWeighting = array_intersect($messageWeighting, $messages);
            $syn = Mage::getModel('epicor_comm/message_request_syn');
            /* @var $syn Epicor_Comm_Model_Message_Request_Syn */

            $syn->addMessageType($sortedMessageWeighting);
            $syn->addLanguage($data['languages']);
            $syn->setTrigger('Admin');
            
            if(!empty($data['stores'])) {
                $websites = array();
                $stores = array();
                foreach($data['stores'] as $storeId) {
                    if(strpos($storeId, 'website_') !== false) {
                        $websites[] = str_replace('website_','',$storeId);
                    } else {
                        $stores[] = str_replace('store_','',$storeId);
                    }
                }
                
                if(!empty($websites)) {
                    $syn->setWebsites($websites);
                }
                
                if(!empty($stores)) {
                    $syn->setStores($stores);
                }
            }

            if (isset($data['sync_type']) && $data['sync_type'] == 'partial') {
                $time = $data['time'][0] . ':' . $data['time'][1] . ':' . $data['time'][2];
                $stringToTime = strtotime($data['date'] . ' ' . $time);
                $helper = Mage::helper('epicor_comm');
                /* @var $helper Epicor_Comm_Helper_Data */
                $UTCDateTime = $helper->UTCwithOffset($stringToTime);
                $syn->setFrom($UTCDateTime);
            }

            if ($syn->sendMessage()) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('SYN request successfully sent'));
                $this->_redirect('*/*/index');
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('SYN request failed. Status description - %s' , $syn->getStatusDescriptionText()));
                $this->_redirect('*/*/index');
            }
        }
    }
}