<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_DlController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function fixAction()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        $select = $connection->select()->from($tablePrefix . 'mageworx_downloads_files');
        $rows = $connection->fetchAll($select);

        foreach ($rows as $row) {
            $file = Mage::helper('mageworx_downloads')->isDownloadsFile($row['file_id']);
            if ($file) {
                $pathInfo = pathinfo(current($file));
                $connection->update(
                    $tablePrefix . 'mageworx_downloads_files',
                    array('filename' => $pathInfo['basename']),
                    'file_id = ' . $row['file_id']
                );
            }
        }
    }

    public function fileAction()
    {
        $fileId = (int)$this->getRequest()->getParam('id');
        $files = Mage::getModel('mageworx_downloads/files')->load($fileId);

        if ($files->getId()) {
            $helper = Mage::helper('mageworx_downloads');

            if (!$helper->checkCustomerGroupAccess($files)) {
                $this->_getSession()->addNotice($helper->__("Requested file not available now"));
                return $this->_redirectReferer();
            }

            $file = $helper->isDownloadsFile($files->getId());
            if (empty($file)) {
                if ($files->getUrl() != '') {
                    Mage::app()->getResponse()->setRedirect($files->getUrl());
                    return;
                } else {
                    $this->_getSession()->addError($helper->__('Sorry, there was an error getting the file'));
                    return $this->_redirectReferer();
                }
            }

            try {
                $fileName    = $helper->getFileName($files);
                $contentType = $helper->getContentType($file[0]);
                $content = array('type'  => 'filename', 'value' => $file[0]);
                $this->_prepareDownloadResponse($fileName, $content, $contentType);
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                return $this->_redirect('/');
            }
        } else {
            $this->_getSession()->addNotice(Mage::helper('mageworx_downloads')->__("Requested file not available now"));
            return $this->_redirectReferer();
        }
    }

    public function getEmbedCodeAction()
    {
        $fileId = (int)$this->getRequest()->getParam('id');
        $file = Mage::getModel('mageworx_downloads/files')->load($fileId);
        if ($file->getId()) {
            $file->setDownloads($file->getDownloads() + 1)->save();
            $html = '<div align="center">' . $file->getEmbedCode() . "</div>";
            $this->getResponse()->setBody($html);
        }

        return;
    }

    public function updateDownloadsAction()
    {
        try {
            $id = $this->getRequest()->getParam('id', 0);
            $file = Mage::getModel('mageworx_downloads/files')->load($id);
            if (!$file || !$file->getId()) {
                return $this;
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();

            $data = array(
                'file_id' => $id,
                'product_id' => $this->getRequest()->getParam('product', 0),
                'customer_id' => $customer->getId(),
                'store_id' => Mage::app()->getStore()->getId(),
                'download_date' => Mage::getModel('core/date')->gmtDate(),
            );

            if ($customer && $customer->getId()) {
                $data['customer_id'] = $customer->getId();
                Mage::getModel('mageworx_downloads/customer')->setData($data)->save();
            } else {
                $update = Mage::getResourceModel('mageworx_downloads/history_collection')->updateDownloadsCount($id);
                if (!$update) {
                    Mage::getModel('mageworx_downloads/customer')->setData($data)->save();
                }
            }

            $file->setDownloads($file->getDownloads() + 1)->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'mageworx_downloads_exceptions.log', true);
        }

        return $this;
    }
}
