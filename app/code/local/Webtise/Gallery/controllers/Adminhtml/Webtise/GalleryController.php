<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 09:23
 */

class Webtise_Gallery_Adminhtml_Webtise_GalleryController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct() {
        $this->setUsedModuleName('Webtise_Gallery');
    }

    protected function _initGallery() {
        $this->_title($this->__('Gallery'))
            ->_title($this->__('Manage Galleries'));

        $galleryId  = (int) $this->getRequest()->getParam('id');
        $gallery    = Mage::getModel('gallery/gallery')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($galleryId) {
            $gallery->load($galleryId);
        }
        Mage::register('current_gallery', $gallery);
        return $gallery;
    }

    public function indexAction() {
        $this->_title($this->__('Gallery'))
            ->_title($this->__('Manage Galleries'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $galleryId  = (int) $this->getRequest()->getParam('id');

        $gallery = $this->_initGallery();
        if ($galleryId && !$gallery->getId()) {
            $this->_getSession()->addError(Mage::helper('gallery')->__('This gallery no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getGalleryData(true)){
            $gallery->setData($data);
        }
        $this->_title($gallery->getTitle());
        Mage::dispatchEvent('gallery_gallery_edit_action', array('gallery' => $gallery));
        $this->loadLayout();
        if ($gallery->getId()){
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('gallery')->__('Default Values'))
                    ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null)));
            }
        }
        else{
            $this->getLayout()->getBlock('left')->unsetChild('store_switcher');
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction() {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $galleryId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $gallery    = $this->_initGallery();
            $galleryData = $this->getRequest()->getPost('gallery', array());
            $gallery->addData($galleryData);
            $gallery->setAttributeSetId($gallery->getDefaultAttributeSetId());
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $gallery->setData($attributeCode, false);
                }
            }
            $categories = $this->getRequest()->getPost('category_ids');
            if ($categories) {
                $categories = implode(',',array_unique(explode(',', $categories)));
                $categories = array('category_ids' => trim($categories, ','));
                $gallery->addData($categories);
            }
            $product_ids = $this->getRequest()->getPost('product_ids');
            if($product_ids) {
                $product_ids = implode(',',array_unique(explode('&', $product_ids)));
                $product_ids = array('product_ids' => $product_ids);
                $gallery->addData($product_ids);
            }
            try {
                $gallery->save();
                $galleryId = $gallery->getId();
                $this->_getSession()->addSuccess(Mage::helper('gallery')->__('Gallery was saved'));
            }
            catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setGalleryData($galleryData);
                $redirectBack = true;
            }
            catch (Exception $e){
                Mage::logException($e);
                $this->_getSession()->addError(Mage::helper('gallery')->__('Error saving gallery'))
                    ->setGalleryData($galleryData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $galleryId,
                '_current'=>true
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            $gallery = Mage::getModel('gallery/gallery')->load($id);
            try {
                $gallery->delete();
                $this->_getSession()->addSuccess(Mage::helper('gallery')->__('The gallery has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    public function massDeleteAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if (!is_array($galleryIds)) {
            $this->_getSession()->addError($this->__('Please select galleries.'));
        }
        else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getSingleton('gallery/gallery')->load($galleryId);
                    Mage::dispatchEvent('gallery_controller_gallery_delete', array('gallery' => $gallery));
                    $gallery->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('gallery')->__('Total of %d record(s) have been deleted.', count($galleryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if(!is_array($galleryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Please select galleries.'));
        }
        else {
            try {
                foreach ($galleryIds as $galleryId) {
                    Mage::getSingleton('gallery/gallery')->load($galleryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d galleries were successfully updated.', count($galleryIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('There was an error updating galleries.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('cms/gallery/gallery');
    }


    public function exportCsvAction() {
        $fileName   = 'galleries.csv';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction() {
        $fileName   = 'gallery.xls';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName   = 'gallery.xml';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function categoriesJsonAction() {
        $this->_initGallery();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('gallery/adminhtml_gallery_helper_categories_content')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    public function productAction() {
        $this->_initGallery();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }

    public function productgridAction() {
        $this->_initGallery();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
}