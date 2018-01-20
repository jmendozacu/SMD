<?php

/**
 * Advanced > Errors Controller
 *
 * This allows the ability to view files in the var/reports folder
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Common_Adminhtml_Epicorcommon_Advanced_ErrorsController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common/advanced')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Error Reports'), Mage::helper('adminhtml')->__('Erp Accounts'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Error Reports'));
        $this->_initAction()
                ->renderLayout();
    }

    public function viewAction() {
        $this->_title($this->__('Error Report View'));
        
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        
        $file = str_replace('.','',$helper->decrypt($helper->urlDecode($this->getRequest()->getParam('report', null))));
        
        if ($file) {
            $filePath = Mage::getBaseDir('var') . DS . 'report' . DS . $file;
            if (file_exists($filePath)) {
                Mage::register('report_filename', $file);
            }
        }
        
        $this->_initAction();
        
        if(is_null(Mage::registry('report_filename'))) {
            $this->_getSession()->addError(Mage::helper('epicor_common')->__('Error report not found'));
            $this->_redirect('*/*/index', array());
        }

        $this->renderLayout();
    }

}
