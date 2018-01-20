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

class Blackbird_ContentManager_Model_Resource_Content_Collection extends Blackbird_ContentManager_Model_Resource_Collection_Abstract
{

    /**
     * ContentManager Content Flat is enabled cache per store
     *
     * @var array
     */
    protected $_flatEnabled                  = array();
    
    
    public function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('contentmanager/content', 'contentmanager/content_flat');
        }
        else {
            $this->_init('contentmanager/content');
        }
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @param unknown_type $entityModel
     */
    protected function _init($model, $entityModel = null)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'contentmanager/content_flat';
        }

        return parent::_init($model, $entityModel);
    }

    /**
     * Set entity to use for attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     */
    public function setEntity($entity)
    {
        if ($this->isEnabledFlat() && ($entity instanceof Mage_Core_Model_Resource_Db_Abstract)) {
            $this->_entity = $entity;
            return $this;
        }
        return parent::setEntity($entity);
    }
    
    /**
     * Retrieve ContentManager Content Flat Helper object
     *
     */
    public function getFlatHelper()
    {
        return Mage::helper('contentmanager/content_flat');
    }

    /**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        // Flat Data can be used only on frontend
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        $storeId = $this->getStoreId();
        if (!isset($this->_flatEnabled[$storeId])) {
            $flatHelper = $this->getFlatHelper();
            $this->_flatEnabled[$storeId] = $flatHelper->isAvailable() && $flatHelper->isBuilt($storeId);
        }
        return $this->_flatEnabled[$storeId];
    }    
    
    protected function _initSelect()
    {
        if($this->getStoreId() == 0) //is in admin
        {
            $adminStoreFilter = Mage::app()->getRequest()->getParam('store');
            $contentTypesCollection = Mage::getModel('contentmanager/contenttype')->getCollection();
            
            $allowedStoreIds = array();
            
            $stores = Mage::helper('contentmanager')->getStores();
            $cctId = (int) Mage::app()->getRequest()->getParam('ct_id');
            
            if($cctId !== 0 && Mage::helper('contentmanager')->isViewAllowed(0, $cctId))
            {
                $allowedStoreIds[] = 0;
            }
            else
            {
                foreach($contentTypesCollection as $contentType)
                {
                    if(Mage::helper('contentmanager')->isViewAllowed(0, $contentType->getId()))
                    {
                        $allowedStoreIds[] = 0;
                    }                    
                }
            }
            
            foreach($stores as $store)
            {
                if($cctId !== 0 && Mage::helper('contentmanager')->isViewAllowed($store->getId(), $cctId) && ($adminStoreFilter == $store->getId() || !$adminStoreFilter))
                {
                    $allowedStoreIds[] = $store->getId();
                }
                else
                {
                    foreach($contentTypesCollection as $contentType)
                    {
                        if(Mage::helper('contentmanager')->isViewAllowed($store->getId(), $contentType->getId()) && ($adminStoreFilter == $store->getId() || !$adminStoreFilter))
                        {
                            $allowedStoreIds[] = $store->getId();
                        }                    
                    }
                }
            }
            $this->addStoresFilter($allowedStoreIds);
        }
        
        //filter
        if($this->getStoreId() > 0)
        {
            $adapter        = $this->getConnection();
            $joinCondition  = array(
                'store.entity_id = e.entity_id'
            );
            
            if ($this->isEnabledFlat()) {
                $this->getSelect()
                    ->from(array('e' => $this->getEntity()->getFlatTableName()))
                    ->joinLeft(
                        array('store' => $this->getTable('contentmanager/contenttype_entity_store')),
                        implode(' AND ', $joinCondition),
                        array())            
                    ->where($adapter->quoteInto('store.store_id = ? OR store.store_id = 0', (int) $this->getStoreId()))
                    ->distinct();
            } else {
                $this->getSelect()
                    ->from(array('e' => $this->getEntity()->getEntityTable()))
                    ->joinLeft(
                        array('store' => $this->getTable('contentmanager/contenttype_entity_store')),
                        implode(' AND ', $joinCondition),
                        array())            
                    ->where($adapter->quoteInto('store.store_id = ? OR store.store_id = 0', (int) $this->getStoreId()))
                    ->distinct();
            }
        }
        else
        {
            if ($this->isEnabledFlat()) {
                $this->getSelect()
                    ->from(array('e' => $this->getEntity()->getFlatTableName()));
            } else {
                $this->getSelect()
                    ->from(array('e' => $this->getEntity()->getEntityTable()));
            }
            
            if($allowedStoreIds)
            {
                $adapter        = $this->getConnection();
                $joinCondition  = array(
                    'store.entity_id = e.entity_id'
                );
                $this->getSelect()
                    ->joinLeft(
                        array('store' => $this->getTable('contentmanager/contenttype_entity_store')),
                        implode(' AND ', $joinCondition),
                        array())
                    ->where($adapter->quoteInto('store.store_id IN ('.implode(',', $allowedStoreIds).')', (int) $this->getStoreId()))
                    ->distinct();
            }
        }
        
        return $this;     
    } 

    /**
     * Load attributes into loaded entities
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_loadAttributes($printQuery, $logQuery);
    }

    /**
     * Add attribute to entities in collection
     * If $attribute=='*' select all attributes
     *
     * @param array|string|integer|Mage_Core_Model_Config_Element $attribute
     * @param false|string $joinType
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if ($this->isEnabledFlat()) {
            if (!is_array($attribute)) {
                $attribute = array($attribute);
            }
            foreach ($attribute as $attributeCode) {
                if ($attributeCode == '*') {
                    foreach ($this->getEntity()->getAllTableColumns() as $column) {
                        $this->getSelect()->columns('e.' . $column);
                        $this->_selectAttributes[$column] = $column;
                        $this->_staticFields[$column]     = $column;
                    }
                } else {
                    $columns = $this->getEntity()->getAttributeForSelect($attributeCode);
                    if ($columns) {
                        foreach ($columns as $alias => $column) {
                            $this->getSelect()->columns(array($alias => 'e.' . $column));
                            $this->_selectAttributes[$column] = $column;
                            $this->_staticFields[$column]     = $column;
                        }
                    }
                }
            }
            return $this;
        }
        return parent::addAttributeToSelect($attribute, $joinType);
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
                ->where('t_d.store_id = ? OR t_d.store_id = 0', $this->getStoreId());
        } else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id = ?', $this->getDefaultStoreId());
        }

        return $select;
    }    

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        $store = Mage::app()->getStore($store);
        $this->setStoreId($store);

        return $this;
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addStoresFilter($stores = null)
    {
        $this->setStoresIds($stores);

        return $this;
    }
    
    /**
     * Add attribute filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
     * @param null|string|array $condition
     * @param string $operator
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($this->isEnabledFlat()) {
            if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
                $attribute = $attribute->getAttributeCode();
            }

            if (is_array($attribute)) {
                $sqlArr = array();
                foreach ($attribute as $condition) {
                    $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
                }
                $conditionSql = '('.join(') OR (', $sqlArr).')';
                $this->getSelect()->where($conditionSql);
                return $this;
            }

            if (!isset($this->_selectAttributes[$attribute])) {
                $this->addAttributeToSelect($attribute);
            }

            if (isset($this->_selectAttributes[$attribute])) {
                $this->getSelect()->where($this->_getConditionSql('e.' . $attribute, $condition));
            }

            return $this;
        }        
        
        return parent::addAttributeToFilter($attribute, $condition, $joinType = 'left'); //inner -> left
    }   
    

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        $storeId = $this->getStoreId();
        
        if ($this->isEnabledFlat()) {
            $column = $this->getEntity()->getAttributeSortColumn($attribute);

            if ($column) {
                $this->getSelect()->order("e.{$column} {$dir}");
            }
            else if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->order($this->_getAttributeFieldName($attribute) . ' ' . $dir);
            }

            return $this;
        } else {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            if ($attrInstance && $attrInstance->usesSource()) {
                $attrInstance->getSource()
                    ->addValueSortToCollection($this, $dir);
                return $this;
            }
        }

        return parent::addAttributeToSort($attribute, $dir);
    }    

    /**
     * Prepare static entity fields
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }

    /**
     * Retrieve collection empty item
     * Redeclared for specifying id field name without getting resource model inside model
     *
     * @return Varien_Object
     */
    public function getNewEmptyItem()
    {
        $object = parent::getNewEmptyItem();
        if ($this->isEnabledFlat()) {
            $object->setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $object;
    }
    
    /**
     * Set Store scope for collection
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setStore($store)
    {
        parent::setStore($store);
        if ($this->isEnabledFlat()) {
            $this->getEntity()->setStoreId($this->getStoreId());
        }
        return $this;
    }
}
