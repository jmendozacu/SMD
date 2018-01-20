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

class Blackbird_ContentManager_Model_Resource_Collection_Abstract extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;
    protected $_storesIds = array();
    

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Blackbird_ContentManager_Model_Resource_Collection_Abstract
     */
    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }
    
    /**
     * Set store filter
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Blackbird_ContentManager_Model_Resource_Collection_Abstract
     */
    public function setStoresIds($stores)
    {
        $this->_storesIds = $stores;
        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return Blackbird_ContentManager_Model_Resource_Collection_Abstract
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoresIds()
    {
        return $this->_storesIds;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Retrieve attributes load select
     *
     * @param string $table
     * @param array|int $attributeIds
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $storeId = $this->getStoreId();

        if ($storeId) {

            $adapter        = $this->getConnection();
            $entityIdField  = $this->getEntity()->getEntityIdField();
            $joinCondition  = array(
                't_s.attribute_id = t_d.attribute_id',
                't_s.entity_id = t_d.entity_id',
                $adapter->quoteInto('t_s.store_id = ?', $storeId)
            );
            $select = $adapter->select()
                ->from(array('t_d' => $table), array($entityIdField, 'attribute_id'))
                ->joinLeft(
                    array('t_s' => $table),
                    implode(' AND ', $joinCondition),
                    array())
                ->where('t_d.entity_type_id = ?', $this->getEntity()->getTypeId())
                ->where("t_d.{$entityIdField} IN (?)", array_keys($this->_itemsById))
                ->where('t_d.attribute_id IN (?)', $attributeIds)
                ->where('t_d.store_id = ?', $this->getStoreId());
        } else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id = ?', $this->getDefaultStoreId());
        }

        return $select;
    }

    /**
     * Load attributes into loaded entities
     *
     * @throws Exception
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items) || empty($this->_itemsById) || empty($this->_selectAttributes)) {
            return $this;
        }

        $entity = $this->getEntity();

        $tableAttributes = array();
        $attributeTypes  = array();
        foreach ($this->_selectAttributes as $attributeCode => $attributeId) {
            if (!$attributeId) {
                continue;
            }
            $attribute = Mage::getSingleton('eav/config')->getCollectionAttribute($entity->getType(), $attributeCode);
            if ($attribute && !$attribute->isStatic()) {
                $tableAttributes[$attribute->getBackendTable()][] = $attributeId;
                if (!isset($attributeTypes[$attribute->getBackendTable()])) {
                    $attributeTypes[$attribute->getBackendTable()] = $attribute->getBackendType();
                }
            }
        }

        $selects = array();
        foreach ($tableAttributes as $table=>$attributes) {
            $select = $this->_getLoadAttributesSelect($table, $attributes);
            $selects[$attributeTypes[$table]][] = $this->_addLoadAttributesSelectValues(
                $select,
                $table,
                $attributeTypes[$table]
            );
        }
        $selectGroups = $this->getLoadAttributesSelectGroups($selects);
        foreach ($selectGroups as $selects) {
            if (!empty($selects)) {
                try {
                    $select = implode(' UNION ALL ', $selects);
                    $values = $this->getConnection()->fetchAll($select);
                } catch (Exception $e) {
                    Mage::printException($e, $select);
                    $this->printLogQuery(true, true, $select);
                    throw $e;
                }

                foreach ($values as $value) {
                    $this->_setItemAttributeValue($value);
                }
            }
        }

        return $this;
    }
    

    /**
     * Groups selects to separate unions depend on type
     *
     * @param array $selects
     * @return array
     */
    public function getLoadAttributesSelectGroups($selects)
    {
        $mainGroup  = array();
        foreach ($selects as $eavType => $selectGroup) {
            $mainGroup = array_merge($mainGroup, $selectGroup);
        }
        return array($mainGroup);
    }    

    /**
     * @param Varien_Db_Select $select
     * @param string $table
     * @param string $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $adapter        = $this->getConnection();
            $valueExpr      = 'IF(t_s.value_id IS NULL, t_d.value, t_s.value)';

            $select->columns(array(
                'default_value' => 't_d.value',
                'store_value'   => 't_s.value',
                'value'         => $valueExpr
            ));
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param string $method
     * @param object $attribute
     * @param string $tableAlias
     * @param array $condition
     * @param string $fieldCode
     * @param string $fieldAlias
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $store_id = $this->_joinAttributes[$fieldCode]['store_id'];
        } else {
            $store_id = $this->getStoreId();
        }

        $adapter = $this->getConnection();

        if ($store_id != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '('.implode(') AND (', $condition).')';
            $defAlias     = $tableAlias . '_default';
            $defAlias     = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias   = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $adapter->quoteInto(
                " AND " . $adapter->quoteColumnAs("$defAlias.store_id", null) . " = ?",
                $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = "IF({$tableAlias}.value_id > 0, ". $fieldAlias.', '. $defFieldAlias.')';
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        } else {
            $store_id = $this->getDefaultStoreId();
        }
        $condition[] = $adapter->quoteInto(
            $adapter->quoteColumnAs("$tableAlias.store_id", null) . ' = ?', $store_id);
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
	


    /**
     * Get SQL for get record count without left JOINs
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        return $this->_getSelectCountSql();
    }

    /**
     * Get SQL for get record count
     *
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();
        $countSelect = (is_null($select)) ?
            $this->_getClearSelect() :
            $this->_buildClearSelect($select);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        if ($resetLeftJoins) {
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }

    /**
     * Retreive clear select
     *
     * @return Varien_Db_Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (is_null($select)) {
            $select = clone $this->getSelect();
        }
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);

        return $select;
    }	
    

    
    /**
     * Clone and reset collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getAllCtIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('e.' . 'ct_id');
        $idsSelect->columns('e.' . 'entity_id');
        $idsSelect->limit($limit, $offset);

        return $idsSelect;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllCtIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllCtIdsSelect($limit, $offset), $this->_bindParams);
    }    
}
