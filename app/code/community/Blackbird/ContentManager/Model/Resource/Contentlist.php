<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Model_Resource_Contentlist extends Mage_Core_Model_Mysql4_Abstract
{   
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store  = null;    
    
    public function _construct(){
        $this->_init('contentmanager/contentlist', 'cl_id');
    }
    
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        // Menu stores
        $condition = array(
                'cl_id = ?'     => (int) $object->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('contentmanager/contentlist_store'), $condition);

        //delete existing blocks
        $collection = Mage::getModel('contentmanager/contentlist_layout_block')->getCollection()->addFieldToFilter('cl_id', $object->getId());
        foreach ($collection as $layoutItem) {
            $layoutItem->delete();
        }
        //delete existing fields
        $collection = Mage::getModel('contentmanager/contentlist_layout_field')->getCollection()->addFieldToFilter('cl_id', $object->getId());
        foreach ($collection as $layoutItem) {
            $layoutItem->delete();
        }
        //delete existing groups
        $collection = Mage::getModel('contentmanager/contentlist_layout_group')->getCollection()->addFieldToFilter('cl_id', $object->getId());
        foreach ($collection as $layoutItem) {
            $layoutItem->delete();
        }

        return parent::_beforeDelete($object);
    }
    
    /**
     * Perform operations before object save
     *
     * @param Mage_Cms_Model_Block $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueContentListToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('A Content List identifier with the same properties already exists in the selected store.'));
        }

        if (! $object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
    
    /**
     * Check for unique of identifier of content list to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueContentListToStores(Mage_Core_Model_Abstract $object)
    {
       
        if (Mage::app()->isSingleStoreMode()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }
        $select = $this->_getReadAdapter()->select()
        ->from(array('contentlist' => $this->getMainTable()))
        ->join(
                        array('contentliststore' => $this->getTable('contentmanager/contentlist_store')),
                        'contentlist.cl_id = contentliststore.cl_id',
                        array()
        )->where('contentlist.url_key = ?', $object->getData('url_key'))
        ->where('contentliststore.store_id IN (?)', $stores);
        
        if ($object->getClId()) {
            $select->where('contentlist.cl_id <> ?', $object->getClId());
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }
    
    
    /**
     * Perform operations after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {        
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('contentmanager/contentlist_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        
        if ($delete) {
            $where = array(
                    'cl_id = ?'     => (int) $object->getId(),
                    'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                        'cl_id'  => (int) $object->getId(),
                        'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }
    
    
    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
        ->from($this->getTable('contentmanager/contentlist_store'), 'store_id')
        ->where('cl_id = :cl_id');

        $binds = array(
                ':cl_id' => (int) $id
        );
        return $adapter->fetchCol($select, $binds);
    }
    
    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }
    
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Cms_Model_Block $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if(Mage_Core_Model_App::ADMIN_STORE_ID != Mage::app()->getStore()->getId()) //if is in front
        {
            //Filter menu by store id on frontend
            if(!$object->getStoreId()) //if store id not specified
            {
                $object = new Varien_Object(array('store_id' => Mage::app()->getStore()->getId()));
            }

            $stores = array(
                (int) $object->getStoreId(),
                Mage_Core_Model_App::ADMIN_STORE_ID,
            );

            $select->join(
                array('contentliststore' => $this->getTable('contentmanager/contentlist_store')),
                $this->getMainTable().'.cl_id = contentliststore.cl_id',
                array('store_id')
            )->where('status = ?', 1)
            ->where('contentliststore.store_id in (?) ', $stores)
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }


    
    
}

