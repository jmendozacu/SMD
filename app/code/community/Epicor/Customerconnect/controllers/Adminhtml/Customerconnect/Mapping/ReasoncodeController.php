<?php

class Epicor_Customerconnect_Adminhtml_Customerconnect_Mapping_ReasoncodeController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'customerconnect/mapping/reasoncode';

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('epicor_common')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Reason Code Mapping'),Mage::helper('adminhtml')->__('Reason Code Mapping'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Reason Code Mapping'));
        $this->_initAction()
            ->renderLayout();
    }


    public function newAction()
    {
        $this->_redirect('*/*/edit', $this->_getStoreParams());
    }

    private function _getStoreParams()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return is_null($storeId) ? array() : array('store' => $storeId);
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('customerconnect/erp_mapping_reasoncode');
        if ($id) {
            $model->load($id);
            if ($model->getCode()) {
                $this->_title($this->__('Edit Mapping "%s"', $model->getCode()));
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerconnect')->__('Mapping does not exist'));
                $this->_redirect('*/*/');
            }
        }
        else {
            $this->_title($this->__('New Mapping'));
        }
        Mage::register('reasoncode_mapping_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('customerconnect/erp_mapping_reasoncode');
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
                    Mage::throwException(Mage::helper('customerconnect')->__('Error saving mapping'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerconnect')->__($model->getCode().' Mapping was successfully saved.'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerconnect')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('customerconnect/erp_mapping_reasoncode');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerconnect')->__('The mapping has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the mapping to delete.'));
        $this->_redirect('*/*/');
    }

    public function exportCsvAction()
    {
        $fileName   = 'reasoncode.csv';
        $content    = $this->getLayout()->createBlock('customerconnect/adminhtml_mapping_reasoncode_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'reasoncode.xml';
        $content    = $this->getLayout()->createBlock('customerconnect/adminhtml_mapping_reasoncode_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}