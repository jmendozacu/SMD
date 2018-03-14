<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Adminhtml_Mageworx_Downloads_CategoriesController extends Mage_Adminhtml_Controller_Action
{
    protected function _init()
    {
        $sessionStore = Mage::getSingleton('adminhtml/session')->getData('store_id');
        if (null !== $this->getRequest()->getParam('store')) {
            Mage::getSingleton('adminhtml/session')->setData('store_id', $this->_getStoreId());
        } elseif (null !== $sessionStore) {
            $this->getRequest()->setParam('store', $sessionStore);
        }
        Mage::register('store_id', $this->_getStoreId());

        $this->_title($this->__('Downloads'))
            ->_title($this->__('Manage Categories'));
    }

    protected function _getStoreId()
    {
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(true)->getId();
        }
        return Mage::app()->getStore((int)$this->getRequest()->getParam('store', 0))->getId();
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('cms/downloads');
        return $this;
    }

    public function indexAction()
    {
        $this->_init();
        $this->_initAction()->renderLayout();
    }

    protected function _redirect($path, $arguments = array())
    {
        $arguments['store'] = $this->_getStoreId();
        parent::_redirect($path, $arguments);
    }

    public function editAction()
    {
        $this->_init();
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mageworx_downloads/categories')->load($id);

        if ($id) {
            $this->_title($model->getTitle());
        } else {
            $this->_title($this->__('New Category'));
        }

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('downloads_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('cms/downloads');
            $this->_addContent($this->getLayout()->createBlock('mageworx_downloads/adminhtml_categories_edit'));
            $this->renderLayout();
        } else {
            $errorMessage = Mage::helper('mageworx_downloads')->__('Categories does not exist');
            Mage::getSingleton('adminhtml/session')->addError($errorMessage);
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $this->_init();
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model = Mage::getModel('mageworx_downloads/categories');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                $successMessage = Mage::helper('mageworx_downloads')->__('Category was successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $errorMessage = Mage::helper('mageworx_downloads')->__('Unable to find Category to save');
        Mage::getSingleton('adminhtml/session')->addError($errorMessage);
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $this->_init();
        $id = (int)$this->getRequest()->getParam('id');

        if (!Mage::helper('mageworx_downloads')->isDefaultCategoryId($id)) {
            try {
                Mage::getSingleton('mageworx_downloads/categories')->setId($id)->delete();

                $successMessage = Mage::helper('mageworx_downloads')->__('Category was successfully deleted');
                Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                $this->_redirect('*/*/');

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $this->_init();
        $categoryIds = $this->getRequest()->getParam('categories');

        if (!is_array($categoryIds)) {
            $errorMessage = Mage::helper('mageworx_downloads')->__('Please select Category(ies)');
            Mage::getSingleton('adminhtml/session')->addError($errorMessage);
        } else {
            try {
                $i = 0;
                foreach ($categoryIds as $categoryId) {
                    if (!Mage::helper('mageworx_downloads')->isDefaultCategoryId($categoryId)) {
                        $category = Mage::getModel('mageworx_downloads/categories')->load($categoryId);
                        $category->delete();
                        ++$i;
                    }
                }
                if ($i) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('mageworx_downloads')->__('Total of %d record(s) were successfully deleted', $i)
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $this->_init();
        $categoryIds = $this->getRequest()->getParam('categories');

        if (!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(ies)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    Mage::getSingleton('mageworx_downloads/categories')
                        ->load($categoryId)
                        ->setIsActive((int)$this->getRequest()->getParam('is_active'))
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($categoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/mageworx_downloads/categories');
    }
}
