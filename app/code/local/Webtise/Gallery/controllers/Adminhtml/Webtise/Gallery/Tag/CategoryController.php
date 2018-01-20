<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 09:23
 */

class Webtise_Gallery_Adminhtml_Webtise_Gallery_Tag_CategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct() {
        $this->setUsedModuleName('Webtise_Gallery');
    }

    protected function _initGalleryTagCategory() {
        $this->_title($this->__('Gallery Tag Category'))
            ->_title($this->__('Manage Gallery Tag Category'));

        $galleryTagCategoryId  = (int) $this->getRequest()->getParam('id');
        $galleryTagCategory = Mage::getModel('gallery/gallery_tag_category')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($galleryTagCategoryId) {
            $galleryTagCategory->load($galleryTagCategoryId);
        }
        Mage::register('current_gallery_tag_category', $galleryTagCategory);
        return $galleryTagCategory;
    }

    public function indexAction() {
        $this->_title($this->__('Gallery Tag Categories'))
            ->_title($this->__('Manage Gallery Tag Categories'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $galleryTagCategoryId  = (int) $this->getRequest()->getParam('id');

        $galleryTagCategory = $this->_initGalleryTagCategory();
        if ($galleryTagCategoryId && !$galleryTagCategory->getId()) {
            $this->_getSession()->addError(Mage::helper('gallery')->__('This gallery no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getGalleryTagCategoryData(true)){
            $galleryTagCategory->setData($data);
        }
        $this->_title($galleryTagCategory->getTitle());
        Mage::dispatchEvent('gallery_gallery_tag_category_edit_action', array('gallery_tag_category' => $galleryTagCategory));
        $this->loadLayout();
        if ($galleryTagCategory->getId()){
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
        $storeId = $this->getRequest()->getParam('store');
        $redirectBack = $this->getRequest()->getParam('back', false);
        $galleryTagCategoryId = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        if ($data) {
            $galleryTagCategory    = $this->_initGalleryTagCategory();
            $galleryTagCategoryData = $this->getRequest()->getPost('gallery_tag_category', array());
            $galleryTagCategory->addData($galleryTagCategoryData);
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $galleryTagCategory->setData($attributeCode, false);
                }
            }
            $tag_ids = $this->getRequest()->getPost('tag_ids');
            if($tag_ids) {
                $tag_ids = implode(',',array_unique(explode('&', $tag_ids)));
                $tag_ids = array(
                    'tag_ids' => $tag_ids
                );
                $galleryTagCategory->addData($tag_ids);
            }
            try {
                $code = Mage::helper('gallery')->normalizeString($galleryTagCategory->getData('title'));
                $galleryTagCategory->addData(
                    array(
                        'code' => $code
                    )
                );
                $galleryTagCategory->save();
                $galleryTagCategoryId = $galleryTagCategory->getId();
                $this->_getSession()->addSuccess(Mage::helper('gallery')->__('Gallery Tag was saved'));
            }
            catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setGalleryData($galleryTagCategoryData);
                $redirectBack = true;
            }
            catch (Exception $e){
                Mage::logException($e);
                $this->_getSession()->addError(Mage::helper('gallery')->__('Error saving Gallery Tag'))
                    ->setGalleryData($galleryTagCategoryData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $galleryTagCategoryId,
                '_current'=>true
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            $galleryTagCategory = Mage::getModel('gallery/gallery_tag_category')->load($id);
            try {
                $galleryTagCategory->delete();
                $this->_getSession()->addSuccess(Mage::helper('gallery')->__('The gallery tag category has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    public function massDeleteAction() {
        $galleryTagCategoryIds = $this->getRequest()->getParam('gallery_tag_category');
        if (!is_array($galleryTagCategoryIds)) {
            $this->_getSession()->addError($this->__('Please select gallery tag categories.'));
        }
        else {
            try {
                foreach ($galleryTagCategoryIds as $galleryTagCategoryId) {
                    $gallery = Mage::getSingleton('gallery/gallery_tag_category')->load($galleryTagCategoryId);
                    Mage::dispatchEvent('gallery_controller_gallery_tag_category_delete', array('gallery_tag_category' => $gallery));
                    $gallery->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('gallery')->__('Total of %d tag category(s) have been deleted.', count($galleryTagCategoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $galleryTagCategoryIds = $this->getRequest()->getParam('gallery_tag_category');
        if(!is_array($galleryTagCategoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Please select gallery tags.'));
        }
        else {
            try {
                foreach ($galleryTagCategoryIds as $galleryTagCategoryId) {
                    Mage::getSingleton('gallery/gallery_tag_category')->load($galleryTagCategoryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d tag(s) were successfully updated.', count($galleryTagCategoryIds)));
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
        return Mage::getSingleton('admin/session')->isAllowed('cms/gallery/gallery/tag');
    }


    public function exportCsvAction() {
        $fileName   = 'gallery_tag_categories.csv';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_category_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction() {
        $fileName   = 'gallery_tag_categories.xls';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_category_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName   = 'gallery_tag_categories.xml';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_category_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function tagAction() {
        $this->_initGalleryTagCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('tag.grid')
            ->setTags($this->getRequest()->getPost('tags', null));
        $this->renderLayout();
    }

    public function taggridAction() {
        $this->_initGalleryTagCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('tag.grid')
            ->setTags($this->getRequest()->getPost('tags', null));
        $this->renderLayout();
    }
}