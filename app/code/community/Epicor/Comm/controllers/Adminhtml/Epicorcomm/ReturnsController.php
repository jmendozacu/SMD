<?php

class Epicor_Comm_Adminhtml_Epicorcomm_ReturnsController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')
                        ->isAllowed('sales/returns');
    }

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction() {
        $return = Mage::getModel('epicor_comm/customer_return')->load($this->getRequest()->get('id'));
        /* @var $quote Epicor_Comm_Model_Customer_Return */

        Mage::register('return', $return);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function logsgridAction() {
//        $this->_initErpAccount();
//        $this->loadLayout(false)->renderLayout();
//        $this->loadLayout();
        
        $return = Mage::getModel('epicor_comm/customer_return')->load($this->getRequest()->get('id'));
        /* @var $quote Epicor_Comm_Model_Customer_Return */

        Mage::register('return', $return);
        
        $block = $this->getLayout()->createBlock('epicor_comm/adminhtml_sales_returns_view_tab_log')
                ->setUseAjax(true);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function updateAction() {
        $content = $this->__('Return Not Found');

        if ($data = $this->getRequest()->getPost()) {
            $return = Mage::getModel('epicor_comm/customer_return')->load($this->getRequest()->get('return_id'));
            /* @var $return Epicor_Comm_Model_Customer_Return */
            if ($return && !$return->isObjectNew()) {
                $return->updateFromErp();
                $content = $this->__('Return Updated From ERP');
            }
        }

        $this->getResponse()->setBody($content);
    }

    public function resendAction() {
        $content = $this->__('Return Not Found');

        if ($data = $this->getRequest()->getPost()) {

            $return = Mage::getModel('epicor_comm/customer_return')->load($this->getRequest()->get('return_id'));
            /* @var $return Epicor_Comm_Model_Customer_Return */
            if ($return && !$return->isObjectNew()) {
                $return->setErpSyncStatus('N');
                $return->save();
                $content = $this->__('Return Will Be Re-Sent Shortly');
            }
        }

        $this->getResponse()->setBody($content);
    }

}
