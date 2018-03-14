<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Files extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('mageworx_downloads/files', 'file_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        $object->setCustomerGroups(implode(',', (array)$object->getCustomerGroups()));
        if (!$object->getId()) {
            $object->setDateAdded(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setDateModified(Mage::getSingleton('core/date')->gmtDate());

        $origData = $object->getOrigData();
        if (!isset($origData)) {
            Mage::getResourceSingleton('mageworx_downloads/relation')->deleteFile($object->getId());
        }
        return parent::_beforeSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::getSingleton('mageworx_downloads/files')->removeDownloadsFile($object->getId());
        Mage::getResourceSingleton('mageworx_downloads/relation')->deleteFile($object->getId());

        return parent::_beforeDelete($object);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $productIds = '';
        if ($object->getId()) {

            if (!is_array($object->getStoreIds())) {
                $object->setStoreIds(explode(',', $object->getStoreIds()));
            }

            $groups = $object->getCustomerGroups();
            if ($groups) {
                $object->setCustomerGroups(explode(',', $groups));
            }

            $product = Mage::getResourceSingleton('mageworx_downloads/relation')->getProductIds($object->getId());
            if ($product) {
                $productIds = implode(',', $product);
            }
            $object->setInProducts($productIds);
        }
        return parent::_afterLoad($object);
    }

    public function getCategoryFiles($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('category_id = ?', $categoryId)
            ->order('name asc');

        return $this->_getReadAdapter()->fetchAssoc($select);
    }

    public function getCountFiles($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(' . $this->getIdFieldName() . ')'))
            ->where('category_id = ?', $categoryId);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}
