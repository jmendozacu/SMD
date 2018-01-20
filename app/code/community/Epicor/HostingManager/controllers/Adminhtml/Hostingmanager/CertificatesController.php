<?php

/**
 * Certificates admin controller
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Adminhtml_Hostingmanager_CertificatesController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    public function indexAction()
    {
        $this->_title($this->__('SSL Certificates'));

        $this->loadLayout();
        $this->_setActiveMenu('epicor_common/hostingmanager');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Manage Hosting'), Mage::helper('customer')->__('Manage Hosting'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $cert = $this->_loadCertificate($id);

        $this->_title($this->__('SSL Certificates'));
        if ($cert->getId())
            $this->_title($this->__('Edit %s', $cert->getName()));
        else
            $this->_title($this->__('Add Certificate'));
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id', null);

            $cert = $this->_loadCertificate($id);
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $helper = Mage::helper('hostingmanager');
            /* @var $helper Epicor_HostingManager_Helper_Data */
            try {
                
                $cert->addData($data);
                $selfCertSuccess = $csrSuccess = true; 
                                
                if ($this->getRequest()->getParam('generate_csr', null)) {
                    $csrSuccess = $cert->generateCSR();
                }
                
                if ($this->getRequest()->getParam('create_ssc', null)) {
                    $selfCertSuccess = $cert->createSelfSignCertificate();
                }

                $cert->save();
    
                
                if($selfCertSuccess === false) {
                    Mage::throwException(Mage::helper('hostingmanager')->__('Error creating self signed certificate'));
                }
                if($csrSuccess === false) {
                    Mage::throwException(Mage::helper('hostingmanager')->__('Error generating certificate signing request'));
                }
                    
                if (!$cert->getId()) {
                    Mage::throwException(Mage::helper('hostingmanager')->__('Error saving certificate'));
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('hostingmanager')->__('Certificate was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $cert->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($cert && $cert->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $cert->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hostingmanager')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $cert = $this->_loadCertificate($id);

        $cert->delete();

        $this->_redirect('*/*/');
    }

    /**
     * 
     * @param int $id
     * @return Epicor_HostingManager_Model_Certificate
     */
    private function _loadCertificate($id)
    {

        $model = Mage::getModel('hostingmanager/certificate');
        /* @var $model Epicor_HostingManager_Model_Certificate */

        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hostingmanager')->__('Certificate does not exist'));
                $this->_redirect('*/*/certificates');
            }
        }

        Mage::register('current_certificate', $model);
        return $model;
    }

}
