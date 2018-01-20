<?php

/**
 * System Logs Controller
 *
 * This allows the ability to view files in the var/log folder
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Common_Adminhtml_Epicorcommon_Advanced_SyslogController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/advanced')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('System Logs'), Mage::helper('adminhtml')->__('Erp Accounts'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('System Log'));
        $this->_initAction()
                ->renderLayout();
    }

    public function viewAction() {
        $this->_title($this->__('System Log View'));
        
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        
        $logFile = $helper->decrypt($helper->urlDecode($this->getRequest()->getParam('log', null)));
        
        if ($logFile) {
            $logFilePath = Mage::getBaseDir('var') . DS . 'log' . DS . $logFile;
            if (file_exists($logFilePath)) {
                Mage::register('log_filename', $logFile);
            }
        } 
        
        $this->_initAction();
        
        if(is_null(Mage::registry('log_filename'))) {
            $this->_getSession()->addError(Mage::helper('epicor_common')->__('Log file not found'));
            $this->_redirect('*/*/index', array());
        }

        $this->renderLayout();
    }

}
