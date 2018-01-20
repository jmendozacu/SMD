<?php

class Epicor_Comm_Block_Adminhtml_Message_Log_View_Log extends Mage_Adminhtml_Block_Template {

    private $log = null;

    /**
     * 
     * @return Epicor_Comm_Model_Message_Log
     */
    public function getLog() {
        if (empty($this->log)) {
            $this->log = Mage::registry('message_log_data');
        }
        return $this->log;
    }

    public function getFirstXml() {
        $log = $this->getLog();
        if ($log->getMessageParent() == 'Upload') {
            return htmlentities(html_entity_decode($log->getXmlIn()));
        } else {
            return htmlentities(html_entity_decode($log->getXmlOut()));
        }
    }

    public function getMessageStatus() {
        $log = $this->getLog();
        $data = $log->getMessageStatus();
        $col = $log->getMessageStatuses();
        $output = $col[$data]? : 'Unknown';
        return $output;
    }

    public function getSecondXml() {
        $log = $this->getLog();
        if ($log->getMessageParent() == 'Upload') {
            return htmlentities(html_entity_decode($log->getXmlOut()));
        } else {
            return htmlentities(html_entity_decode($log->getXmlIn()));
        }
    }

    public function getMessageStatusCode() {
        $log = $this->getLog();
        $code = $log->getStatusCode();
        return $code;
    }

    public function getDate($date) {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        return $helper->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,true);
    }

}