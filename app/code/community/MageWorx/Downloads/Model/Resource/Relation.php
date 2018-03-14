<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mageworx_downloads/relation', 'id');
    }

    public function deleteFile($id)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(),
            $this->_getReadAdapter()->quoteInto('file_id = ?', $id, 'INTEGER')
        );
        return $this;
    }

    public function deleteFilesProduct($productId)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(),
            $this->_getReadAdapter()->quoteInto('product_id = ?', $productId, 'INTEGER')
        );
        return $this;
    }

    public function deleteFileProducts($fileId)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(),
            $this->_getReadAdapter()->quoteInto('file_id = ?', $fileId, 'INTEGER')
        );
        return $this;
    }

    public function getFileIds($productId, $onlyActive = false)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('r' => $this->getMainTable()), new Zend_Db_Expr('DISTINCT r.file_id'));

        if ($onlyActive === true) {
            $files = Mage::getResourceSingleton('mageworx_downloads/files');
            $select->join(array('f' => $files->getMainTable()), 'r.file_id = f.file_id', array())
                ->where('f.is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        }
        $select->where('r.product_id = ?', $productId);

        $result = array();
        $data = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }

    public function getProductIds($fileId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('DISTINCT `product_id`'))
            ->where('file_id = ?', $fileId);

        $result = array();
        $data = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }


    public function getCategoryIds($productId)
    {
        $files = Mage::getResourceSingleton('mageworx_downloads/files');
        $select = $this->_getReadAdapter()->select()
            ->from(array('r' => $this->getMainTable()), new Zend_Db_Expr('DISTINCT f.category_id'))
            ->join(array('f' => $files->getMainTable()), 'r.file_id = f.file_id', array())
            ->where('r.product_id = ?', $productId);

        $result = array();
        $data = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }

}
