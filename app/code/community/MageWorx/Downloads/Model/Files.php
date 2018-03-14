<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Files extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/files');
    }

    public function removeDownloadsFile($fileId, $isRemoveFolder = true)
    {
        $dir = Mage::helper('mageworx_downloads')->getDownloadsPath($fileId);
        $files = Mage::helper('mageworx_downloads')->getFiles($dir);
        if ($files) {
            foreach ($files as $value) {
                unlink($value);
            }
            if ($isRemoveFolder === true) {
                $io = new Varien_Io_File();
                $io->rmdir($dir);
            }
        }
    }

    public function getType()
    {
        return strtolower($this->getData('type'));
    }
}
