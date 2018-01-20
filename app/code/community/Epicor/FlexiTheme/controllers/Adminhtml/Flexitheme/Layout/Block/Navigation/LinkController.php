<?php


class Epicor_FlexiTheme_Adminhtml_Flexitheme_Layout_Block_Navigation_LinkController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    /**
     *
     * @var Epicor_FlexiTheme_Helper_Layout
     */
    private $_helper;
    
    protected $_aclId = 'flexitheme/layout_blocks/themenavigationlinks';
    
    protected function _initAction() {
        $this->_helper =  Mage::helper('flexitheme/layout');
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Navigation Links'));
        $this->loadLayout()
                ->_setActiveMenu('flexitheme/themenavigationlinks')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Navigation Links'), Mage::helper('adminhtml')->__('Navigation Links'));
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
        $model = Mage::getModel('flexitheme/layout_block_link');
        if ($id) {
            $model->load($id);
            if ($model->getDisplayTitle()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Navigation does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('navigation_link_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        $this->_helper =  Mage::helper('flexitheme/layout');
        if ($data = $this->getRequest()->getPost()) {
            
            $model = Mage::getModel('flexitheme/layout_block_link');

            try {
            $id = $this->getRequest()->getParam('link_id');
            if ($id) {
                $model->load($id);
            }
            
            //set block model data
            $model->addData($data);
            //$model->setBlockXml($block_xml);
            
                $model->save();
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Navigation Link'));
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('Navigation Link was successfully saved.'));
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
                $model = Mage::getModel('flexitheme/layout_block_link');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Navigation Link has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Navigation Link to delete.'));
        $this->_redirect('*/*/');
    }
}