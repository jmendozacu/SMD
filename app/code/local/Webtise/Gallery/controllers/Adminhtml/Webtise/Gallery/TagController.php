<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 09:23
 */

class Webtise_Gallery_Adminhtml_Webtise_Gallery_TagController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct() {
        $this->setUsedModuleName('Webtise_Gallery');
    }

    protected function _initGalleryTags() {
        $this->_title($this->__('Gallery Tags'))
            ->_title($this->__('Manage Gallery Tags'));

        $galleryTagId  = (int) $this->getRequest()->getParam('id');
        $galleryTag = Mage::getModel('gallery/gallery_tag')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($galleryTagId) {
            $galleryTag->load($galleryTagId);
        }
        Mage::register('current_gallery_tag', $galleryTag);
        return $galleryTag;
    }

    public function indexAction() {
        $this->_title($this->__('Gallery Tags'))
            ->_title($this->__('Manage Gallery Tags'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $galleryTagId  = (int) $this->getRequest()->getParam('id');

        $galleryTag = $this->_initGalleryTags();
        if ($galleryTagId && !$galleryTag->getId()) {
            $this->_getSession()->addError(Mage::helper('gallery')->__('This gallery no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getGalleryTagData(true)){
            $galleryTag->setData($data);
        }
        $this->_title($galleryTag->getTitle());
        Mage::dispatchEvent('gallery_gallery_tag_edit_action', array('gallery_tag' => $galleryTag));
        $this->loadLayout();
        if ($galleryTag->getId()){
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
        $galleryTagId   = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        if ($data) {
            $galleryTag    = $this->_initGalleryTags();
            $galleryTagData = $this->getRequest()->getPost('gallery_tag', array());
            $galleryTag->addData($galleryTagData);
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $galleryTag->setData($attributeCode, false);
                }
            }
            if (isset($_FILES['image']['name']) && $_FILES['image']['name'] !== '') {

                try {
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $path = Mage::getBaseDir('media') . '/webtise/gallery/tags/' ;
                    $uploader->save($path, $_FILES['image']['name']);
                    $galleryImage = '/webtise/gallery/tags/' . $_FILES['image']['name'];
                    $galleryTag->setImage($galleryImage);
                } catch(Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($e->getMessage())
                        ->setGalleryData($galleryTagData);
                    $redirectBack = true;
                }

            }else if((isset($galleryTagData['image']['delete']) && $galleryTagData['image']['delete'] == 1)){
                $galleryImage['image'] = '';
                $galleryTag->addData($galleryImage);
                unlink(Mage::getBaseDir('media') . DS . $galleryTagData['image']['value']);
                unset($galleryTagData['image']);
            }else {
                $galleryTag->setImage($galleryTagData['image']['value']);
            }
            try {
                $galleryTag->save();
                $galleryTagId = $galleryTag->getId();
                $this->_getSession()->addSuccess(Mage::helper('gallery')->__('Gallery Tag was saved'));
            }
            catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setGalleryData($galleryTagData);
                $redirectBack = true;
            }
            catch (Exception $e){
                Mage::logException($e);
                $this->_getSession()->addError(Mage::helper('gallery')->__('Error saving Gallery Tag'))
                    ->setGalleryData($galleryTagData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $galleryTagId,
                '_current'=>true
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    public function removeFile($file)
    {
        $_helper = Mage::helper('gallery');
        $file = $_helper->updateDirSepereator($file);
        $directory = Mage::getBaseDir('media') . DS .'gallery' ;
        $io = new Varien_Io_File();
        $result = $io->rmdir($directory, true);
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            $gallery = Mage::getModel('gallery/gallery_tag')->load($id);
            try {
                $gallery->delete();
                $this->_getSession()->addSuccess(Mage::helper('gallery')->__('The gallery tag has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    public function massDeleteAction() {
        $galleryTagIds = $this->getRequest()->getParam('gallery_tag');
        if (!is_array($galleryTagIds)) {
            $this->_getSession()->addError($this->__('Please select gallery tags.'));
        }
        else {
            try {
                foreach ($galleryTagIds as $galleryTagId) {
                    $gallery = Mage::getSingleton('gallery/gallery_tag')->load($galleryTagId);
                    Mage::dispatchEvent('gallery_controller_gallery_tag_delete', array('gallery_tag' => $gallery));
                    $gallery->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('gallery')->__('Total of %d tag(s) have been deleted.', count($galleryTagIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $galleryTagIds = $this->getRequest()->getParam('gallery_tag');
        if(!is_array($galleryTagIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Please select gallery tags.'));
        }
        else {
            try {
                foreach ($galleryTagIds as $galleryTagId) {
                    Mage::getSingleton('gallery/gallery_tag')->load($galleryTagId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d tag(s) were successfully updated.', count($galleryTagIds)));
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
        $fileName   = 'gallery_tags.csv';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction() {
        $fileName   = 'gallery_tags.xls';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName   = 'gallery_tags.xml';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_tag_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}
