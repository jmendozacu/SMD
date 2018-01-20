<?php

class Epicor_FlexiTheme_Adminhtml_Flexitheme_LayoutController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected $_aclId = 'flexitheme/theme/themelayout';

    protected function _initAction()
    {
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Layouts'));
        $this->loadLayout()
            ->_setActiveMenu('flexitheme/themelayout')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Theme Layout'),
                Mage::helper('adminhtml')->__('Theme Layout'));
        return $this;
    }

    public function indexAction()
    {

        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('flexitheme/layout');
        if ($id) {
            $model->load($id);
            if ($model->getLayoutName()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Layout does not exist'));
                $this->_redirect('*/*/');
            }
        }

        Mage::register('old_layout_data', $model);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if ($data) {
            $model->setData($data)->setId($id);
        }

        Mage::register('layout_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function activateAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                Mage::helper('flexitheme/layout')->setActiveLayout($id);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Layout has been set to active.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Layout to activate.'));

        $this->_redirect('*/*/');
        return;
    }

    public function saveAction()
    {
        Mage::getSingleton('adminhtml/session')->unsLayoutPageData();

        $error = 'No Data provided';
        $model = Mage::getModel('flexitheme/layout');
        $id = $this->getRequest()->getParam('layout_id');

        if ($data = $this->getRequest()->getPost()) {
            $error = '';


            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            $oldLayoutName = $model->getOrigData('layout_name');
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }

                $validation = $model->validate();

                if ($validation === true) {
                    $model->save();

                    $helper = Mage::helper('flexitheme/layout');
                    /* @var $helper Epicor_FlexiTheme_Helper_Layout */
                    
                    if (!$model->getId()) {
                        Mage::throwException($helper->__('Error saving Layout'));
                    }

                    // Process Page Layouts
                    if ($oldLayoutName != $model->getLayoutName()) {
                        $oldFolder = Mage::getBaseDir('design') . DS . 'frontend' . DS . 'flexitheme' . DS . $helper->safeString($oldLayoutName);
                        $newFolder = Mage::getBaseDir('design') . DS . 'frontend' . DS . 'flexitheme' . DS . $helper->safeString($model->getLayoutName());

                        $file = new Varien_Io_File();
                        $file->open();
                        $file->mv($oldFolder, $newFolder);
                    }
                    $helper->processPageLayouts($data['page'], $model->getId());
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    
                    $mage_config = Mage::getConfig();
                    $current_package = $mage_config->getStoresConfigByPath('design/package/name');
                    $current_layout = $mage_config->getStoresConfigByPath('design/theme/layout');

                    foreach (array_keys($current_package) as $store_id) {
                        if (
                            $current_package[$store_id] == 'flexitheme' && $current_layout[$store_id] == $helper->safeString($model->getLayoutName())
                        ) {
                           $helper->setActiveLayout($model->getId(), $store_id);
                        }
                    }
                } else {
                    $error = implode('|', $validation);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        if (!empty($error)) {
            $response = array(
                'message' => $error,
                'type' => 'error'
            );
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess('Layout was successfully saved');

            $response = array(
                'redirect' => '',
                'type' => 'success'
            );

            // The following line decides if it is a "save" or "save and continue"
            if ($this->getRequest()->getParam('back')) {
                $response['redirect'] = $this->getUrl('*/*/edit', array('id' => $model->getId()));
            } else {
                $response['redirect'] = $this->getUrl('*/*/');
            }
        }

        $this->getResponse()->setBody(json_encode($response));
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('flexitheme/layout')->load($id);
                /* @var $model Epicor_FlexiTheme_Model_Layout */
                $layout_name = Mage::helper('flexitheme')->safeString($model->getLayoutName());
                $model->delete();

                if (!in_array($layout_name, $model->getReservedNames())) {
                    $theme_folder = Mage::getBaseDir('design') . DS . 'frontend' . DS . 'flexitheme' . DS . $layout_name;
                    $file = new Varien_Io_File();
                    $file->open();
                    $file->rmdir($theme_folder, true);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Layout has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Layout to delete.'));
        $this->_redirect('*/*/');
    }

    public function exportAction()
    {
        $helper = Mage::helper('flexitheme/layout');
        /* @var $helper Epicor_FlexiTheme_Helper_Layout */
        
        if ($id = $this->getRequest()->getParam('id')) {

            try {

                $layout = Mage::getModel('flexitheme/layout')->load($id);
                /* @var $layout Epicor_FlexiTheme_Model_Layout */

                $layoutName = $helper->safeString($layout->getLayoutName());
                
                $exportFileName = $layoutName . '-layout-' . date('Ymd-His') . '.layout';
                
                $exportData = $helper->exportLayout($layout);
                
                $this->_prepareDownloadResponse($exportFileName, $exportData);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__('The Layout has been exported.')
                );

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError($helper->__('Unable to find the Layout to export.'));
    }

    public function importAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function importPostAction()
    {
        if ($this->getRequest()->isPost()) {
            // Get Folder names and the like
            try {

                $layoutName = $this->getRequest()->getParam('layout_name');

                $helper = Mage::helper('flexitheme/layout');
                /* @var $helper Epicor_FlexiTheme_Helper_Layout */

                $helper->importLayout($layoutName, $_FILES['layout_file']['tmp_name']);
                Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Layout was successfully imported.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            $this->_redirect('*/*/');
        }
    }
    
}
