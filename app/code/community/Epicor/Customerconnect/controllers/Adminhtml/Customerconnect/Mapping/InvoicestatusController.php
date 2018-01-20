<?php

class Epicor_Customerconnect_Adminhtml_Customerconnect_Mapping_InvoicestatusController extends Epicor_Comm_Controller_Adminhtml_Abstract
{
    protected $_aclId = 'customerconnect/mapping/invoicestatus';

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('customerconnect/mapping/invoicestatus')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Invoice Status Mapping'),Mage::helper('adminhtml')->__('Invoice Status Mapping'));
		return $this;
	}   
 
	public function indexAction() {	
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
        $model = Mage::getModel('customerconnect/erp_mapping_invoicestatus');
        if ($id) {
            $model->load($id);
            if ($model->getCode()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerconnect')->__('Mapping does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('invoicestatus_mapping_data', $model);
 
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
 
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('customerconnect/erp_mapping_invoicestatus');
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
                $model = Mage::getModel('customerconnect/erp_mapping_invoicestatus');
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
        $fileName   = 'invoicestatus.csv';
        $content    = $this->getLayout()->createBlock('customerconnect/adminhtml_mapping_invoicestatus_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'invoicestatus.xml';
        $content    = $this->getLayout()->createBlock('customerconnect/adminhtml_mapping_invoicestatus_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}