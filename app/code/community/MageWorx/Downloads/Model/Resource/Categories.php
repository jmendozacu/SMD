<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Categories extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('mageworx_downloads/categories', 'category_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setStoreId($this->getStoreId());
        if (!$this->isUniqueCategory($object)) {
            $helper = Mage::helper('mageworx_downloads');
            Mage::throwException($helper->__("Category '%s' already exist", $object->getTitle()));
        }
        return parent::_beforeSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        if (!Mage::helper('mageworx_downloads')->isDefaultCategoryId($object->getId())) {
            $files = Mage::getModel('mageworx_downloads/files');
            $data = $files->getResource()->getCategoryFiles($object->getId());
            if ($data) {
                foreach ($data as $file) {
                    $files->load($file[$files->getIdFieldName()])
                        ->setCategoryId(MageWorx_Downloads_Helper_Data::DEFAULT_CATEGORY_ID)
                        ->save();
                }
            }
        }
        return parent::_beforeDelete($object);
    }

    public function isUniqueCategory(Mage_Core_Model_Abstract $object)
    {
        $title = trim($object->getTitle());
        if (!empty($title)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), $this->getIdFieldName())
                ->where('title = ?', $title);
            if ($object->getId()) {
                $select->where($this->getIdFieldName() . ' <> ?', $object->getId());
            }
            if ($this->_getReadAdapter()->fetchRow($select)) {
                return false;
            }
        }
        return true;
    }

    public function getAccessCategories($type = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable());
        if ($type === true) {
            $select->where('is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        }
        $select->order('title ' . Varien_Data_Collection::SORT_ORDER_ASC);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    protected function getStoreId()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::registry('store_id');
        } else {
            return Mage::app()->getStore()->getId();
        }
    }
}
