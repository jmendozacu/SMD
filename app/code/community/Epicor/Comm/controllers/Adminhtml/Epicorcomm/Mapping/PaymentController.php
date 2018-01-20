<?php

class Epicor_Comm_Adminhtml_Epicorcomm_Mapping_PaymentController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'epicor_common/mapping/payment';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_comm/mapping/payment')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Payment Mapping'),Mage::helper('adminhtml')->__('Payment Mapping'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('epicor_comm/erp_mapping_payment');
        if ($id) {
            $model->load($id);
            if ($model->getMagentoCode()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Mapping does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('payment_mapping_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('epicor_comm/erp_mapping_payment');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_comm')->__('Error saving mapping'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('Mapping was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('epicor_comm/erp_mapping_payment');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('The example has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the example to delete.'));
        $this->_redirect('*/*/');
    }

        public function exportCsvAction()
    {
        $fileName   = 'payments.csv';
        $content    = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_payment_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'payments.xml';
        $content    = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_payment_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}