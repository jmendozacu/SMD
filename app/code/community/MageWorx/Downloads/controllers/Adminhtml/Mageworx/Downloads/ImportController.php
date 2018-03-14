<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Adminhtml_Mageworx_Downloads_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $maxUploadSize = Mage::helper('importexport')->getMaxUploadSize();
        $this->_getSession()->addNotice(
            $this->__('Total size of uploadable files must not exceed %s', $maxUploadSize)
        );

        $this->loadLayout();
        $this->renderLayout();
    }

    public function startAction()
    {
        $uploader = new Varien_File_Uploader('downloads_csv');
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $uploader->save(Mage::getBaseDir() . '/var/importexport', 'downloads.csv');

        $files = array();
        try {
            $dirPath = Mage::getBaseDir() . DS . 'media' . DS . 'downloads_import';
            if ($dir = opendir($dirPath)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file == '..' || $file == '.') {
                        continue;
                    }

                    $filePath = $dirPath . DS . $file;
                    if (is_dir($filePath)) {
                        $catId = $file;
                        if (!is_numeric($catId)) {
                            continue;
                        }
                        if ($subDir = opendir($filePath)) {
                            $dirName = $filePath;
                            while (($file = readdir($subDir)) !== false) {
                                if ($file == '..' || $file == '.') {
                                    continue;
                                }
                                $files[] = array('cat_id' => $catId, 'filepath' => $dirName . DS . $file);
                            }
                        }
                    } else {
                        $cat = Mage::getModel('mageworx_downloads/categories')->getCollection()->getFirstItem();
                        if (!($catId = $cat->getId())) {
                            $catId = 0;
                        }
                        $files[] = array('cat_id' => $catId, 'filepath' => $filePath);
                    }

                }
            }

            $this->_getSession()->setFdImportFiles($files);
            $this->_getSession()->setSkippedCnt(0);
            $this->_getSession()->setSkippedIds(array());
            $this->_getSession()->setUploadedFiles(array());
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError(Mage::helper('mageworx_downloads')->__('Sorry, there was an error in upload proccess'));
            return $this->_redirectReferer();
        }
    }

    public function runImportAction()
    {
        @ini_set('max_execution_time', 1800);
        @ini_set('memory_limit', 734003200);
        $limit = Mage::helper('mageworx_downloads')->getImportLimit();
        $current = intval($this->getRequest()->getParam('current', 0));
        $result = array();

        $files = $this->_getSession()->getFdImportFiles();

        $total = count($files);

        $uploadedFiles = $this->_getSession()->getUploadedFiles();

        if ($current < $total) {

            for ($i = $current; $i < ($current + $limit); $i++) {
                if ($i > ($total - 1)) {
                    break;
                }

                $cat = Mage::getModel('mageworx_downloads/categories')->load($files[$i]['cat_id']);
                if (!$cat || !$cat->getId()) {
                    $this->_getSession()->setSkippedCnt($this->_getSession()->getSkippedCnt() + 1);
                    $skippedIds = $this->_getSession()->getSkippedIds();
                    $skippedIds[] = $files[$i]['cat_id'];
                    $this->_getSession()->setSkippedIds($skippedIds);
                    continue;
                }

                $file = pathinfo($files[$i]['filepath']);
                $data = array(
                    'name' => $file['filename'],
                    'type' => isset($file['extension']) ? $file['extension'] : '',
                    'filename' => $file['basename'],
                    'size' => filesize($files[$i]['filepath']),
                    'category_id' => $files[$i]['cat_id'],
                    'store_ids' => 0,
                    'is_active' => 1,
                );
                $fileModel = Mage::getModel('mageworx_downloads/files');
                $fileModel->setData($data);
                $fileModel->save();

                $dest = Mage::helper('mageworx_downloads')->getDownloadsPath($fileModel->getFileId());
                if (!is_dir($dest)) {
                    mkdir($dest, 0777, true);
                }
                if (!copy($files[$i]['filepath'], $dest . $fileModel->getFilename())) {
                    $this->_getSession()->addError($this->__('The file can not be uploaded'));
                }

                $uploadedFiles[$fileModel->getFileId()] = $files[$i]['filepath'];

                if (file_exists($dest . $fileModel->getFilename())) {
                    @unlink($files[$i]['filepath']);
                }
            }

            $this->_getSession()->setUploadedFiles($uploadedFiles);

            $current += $limit;
            if ($current > $total) {
                $current = $total;
            }
            $cnt = $this->_getSession()->getSkippedFiles();
            $result['text'] = $this->__('Total %1$s, processed %2$s file(s) (%3$s%%)...', $total, $current, round($current * 100 / $total, 2));
            $result['url'] = $this->getUrl('*/*/runImport/', array('current' => $current));
        }

        if ($current == $total) {
            if ($total === 0) {
                $result['text'] = $this->__('No files to import');
                $result['stop'] = true;
                $result['url'] = '';
            } else {
                $result['text'] =  $result['text'] = $this->__('Total %1$s, processed %2$s file(s) (%3$s%%). Starting files relations import.', $total, $current, round($current * 100 / $total, 2));
                $result['url'] = $this->getUrl('*/*/importRelations/', array('current' => $current));
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function importRelationsAction()
    {
        $files = $this->_getSession()->getFdImportFiles();
        $total = count($files);
        $uploadedFiles = $this->_getSession()->getUploadedFiles();

        $csvPath = Mage::getBaseDir().'/var/importexport/downloads.csv';
        if (file_exists($csvPath)) {
            Mage::getModel('mageworx_downloads/import')->importFilesRelations($csvPath, $uploadedFiles);
        }

        $skippedCnt = $this->_getSession()->getSkippedCnt();
        $result['text'] = 'Import have been finished successfully';
        $result['skipped_ids'] = implode(',', array_unique($this->_getSession()->getSkippedIds()));
        $result['skipped_cnt'] = $skippedCnt;
        $result['total_imported'] = $total - $skippedCnt;
        $result['stop'] = true;
        $result['url'] = '';

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/import_downloads');
    }
}