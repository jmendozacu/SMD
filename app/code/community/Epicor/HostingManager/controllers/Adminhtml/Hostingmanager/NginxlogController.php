<?php

/**
 * Nginx log admin controller
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Adminhtml_Hostingmanager_NginxlogController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/advanced')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Nginx Logs'), Mage::helper('adminhtml')->__('Erp Accounts'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Nginx Log'));
        $this->_initAction()
                ->renderLayout();
    }

    public function viewAction()
    {
        $this->_title($this->__('Nginx Log View'));

        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $logFile = $helper->decrypt($helper->urlDecode($this->getRequest()->getParam('log', null)));

        if ($logFile) {
            $logFilePath = DS . 'var' . DS . 'log' . DS . 'nginx' . DS;
            if (file_exists($logFilePath)) {
                Mage::register('log_filename', $logFile);
            }
        }

        $this->_initAction();

        if (is_null(Mage::registry('log_filename'))) {
            $this->_getSession()->addError(Mage::helper('epicor_common')->__('Log file not found'));
            $this->_redirect('*/*/index', array());
        }

        $this->renderLayout();
    }

}
