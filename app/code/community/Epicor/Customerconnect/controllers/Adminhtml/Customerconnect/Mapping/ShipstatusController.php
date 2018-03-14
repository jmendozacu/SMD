<?php

/**
 * Controller class for Ship status 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Adminhtml_Customerconnect_Mapping_ShipstatusController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'customerconnect/mapping/shipstatus';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_common')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Ship status Mapping'), Mage::helper('adminhtml')->__('Ship status Mapping'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Ship Status Mapping'));
        $this->_initAction()
                ->renderLayout();
    }

    public function newAction() {
        $this->_redirect('*/*/edit', $this->_getStoreParams());
    }

    private function _getStoreParams() {
        $storeId = (int) $this->getRequest()->getParam('store');
        return is_null($storeId) ? array() : array('store' => $storeId);
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('customerconnect/erp_mapping_shipstatus');
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
        } else {
            $this->_title($this->__('New Mapping'));
        }
        Mage::register('shipstatus_mapping_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model = Mage::getModel('customerconnect/erp_mapping_shipstatus');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                $erpCode = $model->getCode();
            }
            if (array_key_exists('is_default', $data)) {
                $isDefault = 1;
            } else {
                $isDefault = 0;
            }

            $model->setData($data);
            $model->setData('is_default', $isDefault);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                    if ($erpCode != $data['code']) {
                        Mage::throwException(Mage::helper('customerconnect')->__('Ship status code can not be changed'));
                    }
                } else {
                    $collection = $model->getCollection()->addFieldToFilter('code', array('eq' => $data['code']))->addFieldToFilter('store_id', array('eq' => $data['store_id']))->getFirstItem();
                    if ($collection->getId()) {
                        Mage::throwException(Mage::helper('customerconnect')->__('Ship status code already exist'));
                    }
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('customerconnect')->__('Error saving mapping'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerconnect')->__($model->getCode() . ' Mapping was successfully saved.'));
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

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('customerconnect/erp_mapping_shipstatus');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerconnect')->__('The mapping has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the mapping to delete.'));
        $this->_redirect('*/*/');
    }

    public function exportCsvAction() {
        $fileName = 'shipstatus.csv';
        $content = $this->getLayout()->createBlock('customerconnect/adminhtml_mapping_shipstatus_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'shipstatus.xml';
        $content = $this->getLayout()->createBlock('customerconnect/adminhtml_mapping_shipstatus_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

}
