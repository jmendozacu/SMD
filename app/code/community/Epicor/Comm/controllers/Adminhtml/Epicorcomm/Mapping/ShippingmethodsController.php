<?php

class Epicor_Comm_Adminhtml_Epicorcomm_Mapping_ShippingmethodsController extends Mage_Adminhtml_Controller_Action {

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')
                        ->isAllowed('epicor_comm/erp_mapping_shippingmethod');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('epicor_comm/erp_mapping_shippingmethod')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Shipping Method Mapping'),Mage::helper('adminhtml')->__('Shipping Method Mapping'));
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
        $model = Mage::getModel('epicor_comm/erp_mapping_shippingmethod');
        if ($id) {
            $model->load($id);
            if ($model->getShippingMethodCode()) {
                 Mage::register('shippingmethods_mapping_data', new Varien_Object($model->getData()));
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Mapping does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('shippingmethod_mapping_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            Mage::helper('epicor_comm')->getShippingmethodList();
            $activeCarriers = Mage::registry('shipping_carriers');
            if($activeCarriers[$data['shipping_method']]){          // if shipping method missing, don't try to save 
                $model = Mage::getModel('epicor_comm/erp_mapping_shippingmethod');
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                }
                $model->setData($data);
                $model->setShippingMethod($activeCarriers[$data['shipping_method']]);
                $model->setShippingMethodCode($data['shipping_method']);
                $model->setErpCode($data['erp_code']);

                Mage::getSingleton('adminhtml/session')->setFormData($data);
                try {
                    if ($id) {
                        $model->setId($id);
                    }
                    $model->save();

                    if (!$model->getId()) {
                        Mage::throwException(Mage::helper('epicor_comm')->__('Error saving mapping'));
                    }

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('Mapping saved successfully'));
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
            }{
               // this will only be fired if a method is removed between selecting it and saving it - not very likely                
               Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Selected shipping method not available at this time'));
            }
        }else{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('No data found to save'));
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('epicor_comm/erp_mapping_shippingmethod');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('Mapping deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the shipping method to delete.'));
        $this->_redirect('*/*/');
    }

        public function exportCsvAction()
    {
        $fileName   = 'shippingmethods.csv';
        $content    = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_shippingmethods_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'shippingmethods.xml';
        $content    = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_shippingmethods_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * Test Tracking Url 
     * 
     * 
     * 
     * 
     * 
     * @return layout 
     */    
    public function trackingurltestAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
}