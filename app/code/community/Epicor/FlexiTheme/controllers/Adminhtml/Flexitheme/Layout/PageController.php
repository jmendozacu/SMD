<?php


class Epicor_FlexiTheme_Adminhtml_Flexitheme_Layout_PageController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'flexitheme/themeadmin/themelayoutpage';
    
    protected function _initAction() {
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Layout Pages'));
        $this->loadLayout()
                ->_setActiveMenu('flexitheme/themelayoutpage')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Theme Layout Page'), Mage::helper('adminhtml')->__('Theme Layout Pages'));
        return $this;
    }
    
    public function indexAction() {
        
        $this->loadLayout();
        $this->_initAction()
                ->renderLayout();
    }
    
    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('flexitheme/layout_page');
        if ($id) {
            $model->load($id);
            if ($model->getPageName()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Page does not exist'));
                $this->_redirect('*/*/');
            }
        }

        Mage::register('layout_page_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('flexitheme/layout_page');

            $id = $this->getRequest()->getParam('page_id');
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
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Page'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('Page was successfully saved.'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('flexitheme/layout_page');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Page has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Page to delete.'));
        $this->_redirect('*/*/');
    }
}