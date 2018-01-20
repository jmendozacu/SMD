<?php

class Epicor_Comm_Adminhtml_Epicorcomm_Mapping_RemotelinksController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'epicor_common/mapping/remotelinks';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_comm/mapping/remotelinks')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Remotelinks Mapping'),Mage::helper('adminhtml')->__('Remotelinks Mapping'));
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
        $model = Mage::getModel('epicor_comm/erp_mapping_remotelinks');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {                
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Mapping does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('remotelinks_mapping_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('epicor_comm/erp_mapping_remotelinks');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $data['url_pattern'] = strip_tags($data['url_pattern']);
            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if($this->getRequest()->getParam('auth_user')){
                     $model->setHttpAuthorization(true); // set if auth user is set
                }
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
                $model = Mage::getModel('epicor_comm/erp_mapping_remotelinks');
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
        $fileName   = 'currencies.csv';
        $content    = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_remotelinks_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'currencies.xml';
        $content    = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_remotelinks_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function setremotelinkAction()
    {
       $name = $this->getRequest()->getParam('name');  
       Mage::getSingleton('adminhtml/session')->unsRemoteLink($name);
       Mage::getSingleton('adminhtml/session')->setRemoteLink($name);
       //$this->_redirect('*/adminhtml_system_variable/wysiwygPlugin');
    }

}