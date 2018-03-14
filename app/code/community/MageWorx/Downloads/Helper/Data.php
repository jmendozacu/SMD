<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_DOWNLOADS_DISPLAY_SIZE = 'mageworx_downloads/main/display_size';
    const XML_DOWNLOADS_SIZE_PRECISION = 'mageworx_downloads/main/size_precision';
    const XML_DOWNLOADS_GROUP_BY_CATEGORY = 'mageworx_downloads/main/group_by_category';
    const XML_DOWNLOADS_SORT_ORDER = 'mageworx_downloads/main/sort_order';
    const XML_DOWNLOADS_DISPLAY_DOWNLOADS = 'mageworx_downloads/main/display_downloads';
    const XML_DOWNLOADS_HIDE_FILES = 'mageworx_downloads/main/hide_files';
    const XML_DOWNLOADS_PRODUCT_DOWNLOADS_TITLE = 'mageworx_downloads/main/product_downloads_title';
    const XML_DOWNLOADS_FILE_DOWNLOADS_TITLE = 'mageworx_downloads/main/file_downloads_title';
    const XML_DOWNLOADS_BLOCK_POSITION = 'mageworx_downloads/main/block_position';
    const XML_DOWNLOADS_HOW_TO_DOWNLOAD_MESSAGE = 'mageworx_downloads/main/how_to_download_message';
    const XML_DOWNLOADS_ENABLE_FILES_ON_CATEGORY_PAGES = 'mageworx_downloads/main/enable_files_on_category_pages';
    const XML_DOWNLOADS_NAME_SIZE = 'mageworx_downloads/main/name_size';
    const XML_DOWNLOADs_ENABLE_IN_EMAILS = 'mageworx_downloads/main/enable_in_emails';
    const XML_DOWNLOADs_IMPORT_LIMIT = 'mageworx_downloads/main/import_limit';
    const XML_DOWNLOADS_CROP_FILE_NAME_IN_DOWNLOADABLE_LINKS = 'mageworx_downloads/main/crop_file_name_in_downloadable_links';
    const XML_DOWNLOADS_FILE_EXTENSIONS = 'mageworx_downloads/main/file_extensions';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    const ATTACH_USE_CONFIG = 0;
    const ATTACH_ENABLED = 1;
    const ATTACH_DISABLED = 2;

    const ALLOW_GUESTS_CONFIG = 1;
    const ALLOW_GUESTS_ENABLED = 2;
    const ALLOW_GUESTS_DISABLED = 3;

    const DEFAULT_CATEGORY_ID = 1;
    const CUSTOMER_GROUP_ID_FOR_NOT_LOGGET_IN = 0;

    protected $_urlHeaders = array();
    protected $_contentType = 'application/octet-stream';
    protected $_resourceFile = null;
    protected $_handle = null;

    public function getStatusArray()
    {
        return array(
            self::STATUS_ENABLED => $this->__('Enabled'),
            self::STATUS_DISABLED => $this->__('Disabled'),
        );
    }

    public function getAttachArray()
    {
        return array(
            self::ATTACH_USE_CONFIG => $this->__('Use Config'),
            self::ATTACH_ENABLED => $this->__('Yes'),
            self::ATTACH_DISABLED => $this->__('No'),
        );
    }

    public function isDefaultCategoryId($id)
    {
        return (bool)(self::DEFAULT_CATEGORY_ID == $id);
    }

    public function isDisplaySize()
    {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_DISPLAY_SIZE);
    }

    public function getNameSize()
    {
        return Mage::getStoreConfig(self::XML_DOWNLOADS_NAME_SIZE);
    }

    public function isEnableFilesOnCategoryPages()
    {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_ENABLE_FILES_ON_CATEGORY_PAGES);
    }

    public function isEnabledInEmails()
    {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADs_ENABLE_IN_EMAILS);
    }

    public function isHideFiles()
    {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_HIDE_FILES);
    }

    public function getSizePrecision()
    {
        return Mage::getStoreConfig(self::XML_DOWNLOADS_SIZE_PRECISION);
    }

    public function getGroupByCategory()
    {
        return Mage::getStoreConfig(self::XML_DOWNLOADS_GROUP_BY_CATEGORY);
    }

    public function getSortOrder()
    {
        return Mage::getStoreConfig(self::XML_DOWNLOADS_SORT_ORDER);
    }

    public function isDisplayDownloads()
    {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_DISPLAY_DOWNLOADS);
    }

    public function getImportLimit()
    {
        return Mage::getStoreConfig(self::XML_DOWNLOADs_IMPORT_LIMIT);
    }

    public function getProductDownloadsTitle()
    {
        $title = trim(Mage::getStoreConfig(self::XML_DOWNLOADS_PRODUCT_DOWNLOADS_TITLE));
        if (empty($title)) {
            $title = $this->__('Product Downloads');
        }
        return $title;
    }

    public function getHowToDownloadMessage()
    {
        $title = trim(Mage::getStoreConfig(self::XML_DOWNLOADS_HOW_TO_DOWNLOAD_MESSAGE));
        if (empty($title)) {
            $title = $this->__('You have to %login% or %register% to download this file');
        }

        $customerHelper =  Mage::helper('customer');

        $login = "<a href=" . $customerHelper->getLoginUrl() . ">" . $customerHelper->__('Login') . "</a>";
        $register = "<a href=" . $customerHelper->getRegisterUrl() . ">" . $customerHelper->__('Register') . "</a>";
        $title = str_replace('%login%', $login, $title);
        $title = str_replace('%register%', $register, $title);

        return $title;
    }

    public function getFileDownloadsTitle()
    {
        $title = trim(Mage::getStoreConfig(self::XML_DOWNLOADS_FILE_DOWNLOADS_TITLE));
        if (empty($title)) {
            $title = $this->__('File Downloads');
        }
        return $title;
    }

    public function getBlockPosition()
    {
        return explode(',', Mage::getStoreConfig(self::XML_DOWNLOADS_BLOCK_POSITION));
    }

    /**
     *
     * @return bool
     */
    public function isCropFileNameInDownloadableLinks()
    {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_CROP_FILE_NAME_IN_DOWNLOADABLE_LINKS);
    }

    /**
     *
     * Returns an array of elements in lowercase
     * @return array
     */
    public function getIgnoreFileExtensions()
    {
        $ignoreFileExtensions = array_filter(
            preg_split(
                '/\r?\n/',
                Mage::getStoreConfig(self::XML_DOWNLOADS_FILE_EXTENSIONS)
            )
        );
        $ignoreFileExtensions = array_filter(array_map('trim', $ignoreFileExtensions));

        return array_map('strtolower', $ignoreFileExtensions);
    }

    public function getFilter($data)
    {
        $result = array();
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_StringTrim());
//       	$filter->addFilter(new Zend_Filter_StripTags());

        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->getFilter($value);
                } else {
                    $result[$key] = $filter->filter($value);
                }
            }
        }
        return $result;
    }

    public function getDateFormat()
    {
        return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    public function getFiles($path)
    {
        return @glob($path . "*.*");
    }

    public function getDownloadsPath($fileId)
    {
        return Mage::getBaseDir('media') . DS . 'downloads' . DS . $fileId . DS;
    }

    public function isDownloadsFile($fileId)
    {
        return $this->getFiles($this->getDownloadsPath($fileId));
    }

    public function prepareFileName($string)
    {
        $result = preg_replace(
            array('/[^\w]/i', '/[ ]{2,}/', '/[ ]/', '/[_]{1,}/'),
            array(' ', ' ', '_', '_'),
            Mage::helper('catalog/product_url')->format($string)
        );
        $result = trim($result, '_');
        return strtolower($result);
    }

    public function getFileName(Varien_Object $item)
    {
        return $this->prepareFileName($item->getName()) . '.' . strtolower($item->getType());
    }

    protected function _getHandle()
    {
        if (!$this->_resourceFile) {
            Mage::throwException($this->__('Please set resource file and link type'));
        }
        if (null === $this->_handle) {
            $this->_handle = new Varien_Io_File();
            $this->_handle->open(array('path' => Mage::getBaseDir('var')));
            if (!$this->_handle->fileExists($this->_resourceFile, true)) {
                Mage::throwException($this->__('File does not exist'));
            }
            $this->_handle->streamOpen($this->_resourceFile, 'r');
        }
        return $this->_handle;
    }

    public function getFileType($filePath)
    {
        $ext = substr($filePath, strrpos($filePath, '.') + 1);
        $type = Mage::getConfig()->getNode('global/mime/types/x' . $ext);
        if ($type) {
            return $type;
        }
        return $this->_contentType;
    }

    public function getContentType($resource)
    {
        $this->_resourceFile = $resource;

        $this->_getHandle();
        if (function_exists('mime_content_type')) {
            return mime_content_type($this->_resourceFile);
        }

        return $this->getFileType($this->_resourceFile);
    }

    public function prepareFileSize($size)
    {
        $parsedSize = 0;
        $type = '';
        $round = 1;
        $b = $this->__('B');
        $kb = $this->__('KB');
        $mb = $this->__('MB');
        $kbSize = 1024;
        $mbSize = 1024 * 1024;

        switch ($this->getSizePrecision()) {
            case MageWorx_Downloads_Model_Filesize::FILE_SIZE_PRECISION_AUTO:
                if ($size >= $kbSize && $size < $mbSize) {
                    $parsedSize = $size / $kbSize;
                    $type = $kb;
                } elseif ($size >= $mbSize) {
                    $parsedSize = $size / $mbSize;
                    $type = $mb;
                } else {
                    $parsedSize = $size;
                    $type = $b;
                    $round = 0;
                }
                break;

            case MageWorx_Downloads_Model_Filesize::FILE_SIZE_PRECISION_MEGA:
                $parsedSize = $size / $mbSize;
                $type = $mb;
                $round = 2;
                break;

            case MageWorx_Downloads_Model_Filesize::FILE_SIZE_PRECISION_KILO:
                $parsedSize = $size / $kbSize;
                $type = $kb;
                break;

            default:
                $parsedSize = $size;
                $type = $b;
                $round = 0;
                break;
        }
        return round($parsedSize, $round) . ' ' . $type;
    }

    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function checkCustomerAccess($data)
    {
        if (!$this->isHideFiles()) {
            return true;
        }

        $access = false;
        foreach ($data as $item) {
            if (is_array($item)) {
                $checkAccess = $this->checkCustomerAccess($item['files']);
            } else {
                $checkAccess = $this->checkCustomerGroupAccess($item);
            }

            if ($checkAccess === true) {
                $access = true;
                break;
            }
        }
        return $access;
    }

    public function showAccessNotice($data)
    {
        if (!is_array($data)) {
            return false;
        }

        $result = true;
        foreach ($data as $item) {
            if (is_array($item)) {
                $checkAccess = !$this->showAccessNotice($item['files']);
            } else {
                $checkAccess = $this->checkCustomerGroupAccess($item);
            }

            if ($checkAccess == false) {
                $result = false;
                break;
            }
        }

        return !$result;
    }

    public function checkCustomerGroupAccess(Varien_Object $item)
    {
        $access = true;
        if ($item->getIsActive() == self::STATUS_DISABLED) {
            return false;
        }
        $groups = $item->getCustomerGroups();
        if (null === $groups && $item->getAllowGuests()) {
            $groups = '0';
        }

        if (!empty($groups) && is_string($groups)) {
            $groups = explode(',', $groups);
        }
        if (empty($groups)) {
            $access = true;
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $id = Mage::getSingleton('customer/session')->getId();
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();

            if ($id != $customerId) {
                $customerGroupId = self::CUSTOMER_GROUP_ID_FOR_NOT_LOGGET_IN;
            }

            if (is_array($groups) && !in_array($customerGroupId, $groups)) {
                $access = false;
            }
        }

        $limit = $item->getDownloadsLimit();
        if ($limit) {
            if ($item->getDownloads() >= $limit) {
                $access = false;
            }
        }

        return $access;
    }

    protected function _prepareIconPath($path)
    {
        return ltrim(str_replace(DS, '/', $path), '/');
    }

    protected function _getIconsPath($iconName)
    {
        return DS . 'mageworx' . DS . 'downloads' . DS . 'types' . DS . $iconName;
    }

    public function getIconPath($iconName)
    {
        return Mage::getBaseDir('media') . $this->_getIconsPath($iconName);
    }

    public function getIconUrl($name)
    {
        $iconName = strtolower($name) . '.png';
        $filePath = $this->getIconPath($iconName);
        if (!file_exists($filePath)) {
            $iconName = 'default.png';
        }
        return Mage::getBaseUrl('media') . $this->_prepareIconPath($this->_getIconsPath($iconName));
        //Mage::getDesign()->getSkinUrl(
        //$this->_prepareIconPath($this->_getIconsPath($iconName)), array('_area' => 'frontend')
        //);
    }

    public function getIcon($item)
    {
        $name = $item->getType();
        if ($name) {
            return '<img src="' . $this->getIconUrl($name) . '" alt="' . $name . '"/>';
        }
    }

    public function getDownloadLink(Varien_Object $item)
    {
        $prod = Mage::registry('current_product');
        $productId = ($prod && $prod->getId()) ? $prod->getId() : 0;
        $linkPart = $this->getFileName($item);

        if ($this->isCropFileNameInDownloadableLinks()) {
            $fileExtension = strtolower($item->getType());
            $ignoreFileExtensions = $this->getIgnoreFileExtensions();

            if (!$ignoreFileExtensions || in_array($fileExtension, $ignoreFileExtensions)) {
                $linkPart = '';
            }
        }

        return Mage::getUrl('downloads/dl/file/', array('id' => $item->getFileId(), 'product' => $productId)) . $linkPart;
    }

    public function getUpdateCountUrl()
    {
        $prod = Mage::registry('current_product');
        $prodId = ($prod && $prod->getId()) ? $prod->getId() : 0;

        return Mage::getUrl('downloads/dl/updateDownloads', array('product' => $prodId, 'id' => '%file_id%'));
    }

    public function getEmbedLink(Varien_Object $item)
    {
        return Mage::getUrl('downloads/dl/getEmbedCode', array('id' => $item->getFileId()));
    }

    public function getDownloadItemHtml($file, $isGridMode)
    {
        $block = Mage::app()->getLayout()->createBlock('core/template')->setTemplate('mageworx/downloads/link.phtml');
        $block->setData('item', $file);
        $block->setData('is_grid_mode', $isGridMode);

        return $block->toHtml();
    }
}
