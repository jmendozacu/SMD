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

class Blackbird_ContentManager_Model_Resource_Menu extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store  = null;    

    public function _construct(){
            $this->_init('contentmanager/menu', 'menu_id');
    }

    /**
     * Process block data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        // Menu stores
        $condition = array(
                'menu_id = ?'     => (int) $object->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('contentmanager/menu_store'), $condition);

        // Menu nodes
        $condition = array(
                'menu_id = ?'     => (int) $object->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('contentmanager/menu_node'), $condition);

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
        if (!$this->getIsUniqueMenuToStores($object)) {
            Mage::throwException(Mage::helper('cms')->__('A menu identifier with the same properties already exists in the selected store.'));
        }

        if (! $object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     * Perform operations after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if(!Mage::helper('contentmanager')->isMenuAllowed(0) && Mage::app()->getStore()->getId() == 0)
        {
            return parent::_afterSave($object);
        }
        
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('contentmanager/menu_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                    'menu_id = ?'     => (int) $object->getId(),
                    'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                        'menu_id'  => (int) $object->getId(),
                        'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param Mage_Core_Model_Abstract $object
     * @param mixed $value
     * @param string $field
     * @return Mage_Cms_Model_Resource_Block
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
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
                array('menustore' => $this->getTable('contentmanager/menu_store')),
                $this->getMainTable().'.menu_id = menustore.menu_id',
                array('store_id')
            )->where('status = ?', 1)
            ->where('menustore.store_id in (?) ', $stores)
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of menu to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueMenuToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getReadAdapter()->select()
        ->from(array('menu' => $this->getMainTable()))
        ->join(
                        array('menustore' => $this->getTable('contentmanager/menu_store')),
                        'menu.menu_id = menustore.menu_id',
                        array()
        )->where('menu.identifier = ?', $object->getData('identifier'))
        ->where('menustore.store_id IN (?)', $stores);

        if ($object->getId()) {
            $select->where('menu.menu_id <> ?', $object->getId());
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
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
        ->from($this->getTable('contentmanager/menu_store'), 'store_id')
        ->where('menu_id = :menu_id');

        $binds = array(
                ':menu_id' => (int) $id
        );

        return $adapter->fetchCol($select, $binds);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Resource_Page
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }
}

