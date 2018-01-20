<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Blackbird_ContentManager
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ContentManager Content Flat Indexer Resource Model
 *
 * @category    Mage
 * @package     Blackbird_ContentManager
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer extends Mage_Index_Model_Resource_Abstract
{
    const XML_NODE_MAX_INDEX_COUNT  = 'global/contentmanager/content/flat/max_index_count';
    const XML_NODE_ATTRIBUTE_NODES  = 'global/contentmanager/content/flat/attribute_nodes';

    /**
     * Attribute codes for flat
     *
     * @var array
     */
    protected $_attributeCodes;

    /**
     * Attribute objects for flat cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Required system attributes for preload
     *
     * @var array
     */
    protected $_systemAttributes     = array('ct_id', 'title', 'url_key', 'status', 'created_at', 'updated_at');

    /**
     * Eav ContentManager_Content Entity Type Id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Flat table columns cache
     *
     * @var array
     */
    protected $_columns;

    /**
     * Flat table indexes cache
     *
     * @var array
     */
    protected $_indexes;

    /**
     * Content Type Instances cache
     *
     * @var array
     */
    protected $_contentTypes;

    /**
     * Exists flat tables cache
     *
     * @var array
     */
    protected $_existsFlatTables     = array();

    /**
     * Flat tables which were prepared
     *
     * @var array
     */
    protected $_preparedFlatTables   = array();

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('contentmanager/content', 'entity_id');
    }

    /**
     * Rebuild ContentManager Content Flat Data
     *
     * @param Mage_Core_Model_Store|int $store
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function rebuild($store = null)
    {
        if ($store === null) {
            foreach (Mage::app()->getStores() as $store) {
                $this->rebuild($store->getId());
            }
            return $this;
        }

        $storeId = (int)Mage::app()->getStore($store)->getId();

        $this->prepareFlatTable($storeId);
        $this->cleanNonWebsiteContents($storeId);
        $this->updateStaticAttributes($storeId);
        $this->updateEavAttributes($storeId);
        $this->updateEventAttributes($storeId);
        $this->updateRelationContents($storeId);
        $this->cleanRelationContents($storeId);

        $flag = $this->getFlatHelper()->getFlag();
        $flag->setIsBuilt(true)->setStoreBuilt($storeId, true)->save();
        return $this;
    }

    /**
     * Retrieve ContentManager Content Flat helper
     *
     * @return Blackbird_ContentManager_Helper_Content_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('contentmanager/content_flat');
    }

    /**
     * Retrieve attribute codes using for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        if ($this->_attributeCodes === null) {
            $adapter               = $this->_getReadAdapter();
            $this->_attributeCodes = array();

            $attributeNodes = Mage::getConfig()
                ->getNode(self::XML_NODE_ATTRIBUTE_NODES)
                ->children();
            foreach ($attributeNodes as $node) {
                $attributes = Mage::getConfig()->getNode((string)$node)->asArray();
                $attributes = array_keys($attributes);
                $this->_systemAttributes = array_unique(array_merge($attributes, $this->_systemAttributes));
            }

            $bind = array(
                'entity_type_id'    => $this->getEntityTypeId()
            );

            $select = $adapter->select()
                ->from(array('main_table' => $this->getTable('eav/attribute')))
                ->join(
                    array('additional_table' => $this->getTable('contentmanager/eav_attribute')),
                    'additional_table.attribute_id = main_table.attribute_id'
                )
                ->where('main_table.entity_type_id = :entity_type_id');
            
            $attributesData = $adapter->fetchAll($select, $bind);
            Mage::getSingleton('eav/config')
                ->importAttributesData($this->getEntityType(), $attributesData);

            foreach ($attributesData as $data) {
                $this->_attributeCodes[$data['attribute_id']] = $data['attribute_code'];
            }
            unset($attributesData);
        }

        return $this->_attributeCodes;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return Blackbird_ContentManager_Model_Content::ENTITY;
    }

    /**
     * Retrieve ContentManager Entity Type Id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = Mage::getModel('contentmanager/content')
                ->getEntityTypeId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve attribute objects for flat
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = array();
            $attributeCodes    = $this->getAttributeCodes();
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();

            foreach ($attributeCodes as $attributeCode) {
                $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute($this->getEntityType(), $attributeCode)
                    ->setEntity($entity);
                try {
                    // check if exists source and backend model.
                    // To prevent exception when some module was disabled
                    $attribute->usesSource() && $attribute->getSource();
                    $attribute->getBackend();
                    $this->_attributes[$attributeCode] = $attribute;
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        return $this->_attributes;
    }

    /**
     * Retrieve loaded attribute by code
     *
     * @param string $attributeCode
     * @throws Mage_Core_Exception
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (!isset($attributes[$attributeCode])) {
            $attribute = Mage::getModel('contentmanager/resource_eav_attribute')
                ->loadByCode($this->getEntityTypeId(), $attributeCode);
            if (!$attribute->getId()) {
                Mage::throwException(Mage::helper('contentmanager')->__('Invalid attribute %s', $attributeCode));
            }
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();
            $attribute->setEntity($entity);

            return $attribute;
        }

        return $attributes[$attributeCode];
    }

    /**
     * Retrieve ContentManager Content Flat Table name
     *
     * @param int $storeId
     * @return string
     */
    public function getFlatTableName($storeId)
    {
        return sprintf('%s_%s', $this->getTable('contentmanager/content_flat'), $storeId);
    }

    /**
     * Retrieve contentmanager content flat columns array in old format (used before MMDB support)
     *
     * @return array
     */
    protected function _getFlatColumnsOldDefinition()
    {
        $columns = array();
        $columns['entity_id'] = array(
            'type'      => 'int(10)',
            'unsigned'  => true,
            'is_null'   => false,
            'default'   => null,
            'extra'     => null
        );

        return $columns;
    }

    /**
     * Retrieve contentmanager content flat columns array in DDL format
     *
     * @return array
     */
    protected function _getFlatColumnsDdlDefinition()
    {
        $columns = array();
        $columns['entity_id'] = array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'    => null,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => false,
            'primary'   => true,
            'comment'   => 'Entity Id'
        );

        return $columns;
    }

    /**
     * Retrieve contentmanager content flat table columns array
     *
     * @return array
     */
    public function getFlatColumns()
    {
        if ($this->_columns === null) {
            if (Mage::helper('core')->useDbCompatibleMode()) {
                $this->_columns = $this->_getFlatColumnsOldDefinition();
            } else {
                $this->_columns = $this->_getFlatColumnsDdlDefinition();
            }

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
                $columns = $attribute
                    ->setFlatAddFilterableAttributes($this->getFlatHelper()->isAddFilterableAttributes())
                    ->setFlatAddChildData($this->getFlatHelper()->isAddChildData())
                    ->getFlatColumns();
                if ($columns !== null) {
                    $this->_columns = array_merge($this->_columns, $columns);
                }
            }

            $columnsObject = new Varien_Object();
            $columnsObject->setColumns($this->_columns);
            Mage::dispatchEvent('contentmanager_content_flat_prepare_columns',
                array('columns' => $columnsObject)
            );
            $this->_columns = $columnsObject->getColumns();
        }

        return $this->_columns;
    }

    /**
     * Retrieve contentmanager content flat table indexes array
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        if ($this->_indexes === null) {
            $this->_indexes = array();

            if ($this->getFlatHelper()->isAddChildData()) {
                $this->_indexes['PRIMARY'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id', 'child_id')
                );
                $this->_indexes['IDX_CHILD'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                    'fields' => array('child_id')
                );
                $this->_indexes['IDX_IS_CHILD'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                    'fields' => array('entity_id', 'is_child')
                );
            } else {
                $this->_indexes['PRIMARY'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id')
                );
            }

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute */
                $indexes = $attribute
                    ->setFlatAddFilterableAttributes($this->getFlatHelper()->isAddFilterableAttributes())
                    ->setFlatAddChildData($this->getFlatHelper()->isAddChildData())
                    ->getFlatIndexes();
                if ($indexes !== null) {
                    $this->_indexes = array_merge($this->_indexes, $indexes);
                }
            }

            $indexesObject = new Varien_Object();
            $indexesObject->setIndexes($this->_indexes);
            Mage::dispatchEvent('contentmanager_content_flat_prepare_indexes', array(
                'indexes'   => $indexesObject
            ));
            $this->_indexes = $indexesObject->getIndexes();
        }

        return $this->_indexes;
    }

    /**
     * Compare Flat style with Describe style columns
     * If column a different - return false
     *
     * @param array $column
     * @param array $describe
     * @return bool
     */
    protected function _compareColumnProperties($column, $describe)
    {
        return Mage::getResourceHelper('catalog')->compareIndexColumnProperties($column, $describe);
    }

    /**
     * Retrieve column definition fragment
     * @deprecated since 1.5.0.0
     *
     * Example: `field_name` smallint(5) unsigned NOT NULL default '0'
     *
     * @param string $fieldName
     * @param array $fieldProp
     * @return string
     */
    protected function _sqlColunmDefinition($fieldName, $fieldProp)
    {
        $fieldNameQuote = $this->_getWriteAdapter()->quoteIdentifier($fieldName);

        /**
         * Process the case when 'is_null' prohibits null value, and 'default' proposed to be null
         * It just means that default value not specified
         */
        if ($fieldProp['is_null'] === false && $fieldProp['default'] === null) {
            $defaultValue = '';
        } else {
            $defaultValue = $fieldProp['default'] === null ? ' DEFAULT NULL' : $this->_getReadAdapter()
                ->quoteInto(' DEFAULT ?', $fieldProp['default']);
        }

        return "{$fieldNameQuote} {$fieldProp['type']}"
            . ($fieldProp['unsigned'] ? ' UNSIGNED' : '')
            . ($fieldProp['extra'] ? ' ' . $fieldProp['extra'] : '')
            . ($fieldProp['is_null'] === false ? ' NOT NULL' : '')
            . $defaultValue;
    }

    /**
     * Retrieve index definition fragment
     * @deprecated since 1.5.0.0
     *
     * Example: INDEX `IDX_NAME` (`field_id`)
     *
     * @param string $indexName
     * @param array $indexProp
     * @return string
     */
    protected function _sqlIndexDefinition($indexName, $indexProp)
    {
        $fields = $indexProp['fields'];
        if (is_array($fields)) {
            $fieldSql = array();
            foreach ($fields as $field) {
                $fieldSql[] = $this->_getReadAdapter()->quoteIdentifier($field);
            }
            $fieldSql = join(',', $fieldSql);
        }
        else {
            $fieldSql = $this->_getReadAdapter()->quoteIdentifier($fields);
        }

        $indexNameQuote = $this->_getReadAdapter()->quoteIdentifier($indexName);
        switch (strtolower($indexProp['type'])) {
            case 'primary':
                $condition = 'PRIMARY KEY';
                break;
            case 'unique':
                $condition = 'UNIQUE ' . $indexNameQuote;
                break;
            case 'fulltext':
                $condition = 'FULLTEXT ' . $indexNameQuote;
                break;
            default:
                $condition = 'INDEX ' . $indexNameQuote;
                break;
        }

        return sprintf('%s (%s)', $condition, $fieldSql);
    }

    /**
     * Retrieve UNIQUE HASH for a Table foreign key
     *
     * @param string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return Mage::getSingleton('core/resource')
            ->getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * Prepare flat table for store
     *
     * @param int $storeId
     * @throws Mage_Core_Exception
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function prepareFlatTable($storeId)
    {
        if (isset($this->_preparedFlatTables[$storeId])) {
            return $this;
        }
        $adapter   = $this->_getWriteAdapter();
        $tableName = $this->getFlatTableName($storeId);

        // Extract columns we need to have in flat table
        $columns = $this->getFlatColumns();
        if (Mage::helper('core')->useDbCompatibleMode()) {
             /* Convert old format of flat columns to new MMDB format that uses DDL types and definitions */
            foreach ($columns as $key => $column) {
                $columns[$key] = Mage::getResourceHelper('core')->convertOldColumnDefinition($column);
            }
        }

        // Extract indexes we need to have in flat table
        $indexesNeed  = $this->getFlatIndexes();

        $maxIndex = Mage::getConfig()->getNode(self::XML_NODE_MAX_INDEX_COUNT);
        if (count($indexesNeed) > $maxIndex) {
            Mage::throwException(Mage::helper('contentmanager')->__("The Flat ContentManager module has a limit of %2\$d filterable and/or sortable attributes. Currently there are %1\$d of them. Please reduce the number of filterable/sortable attributes in order to use this module", count($indexesNeed), $maxIndex));
        }

        // Process indexes to create names for them in MMDB-style and reformat to common index definition
        $indexKeys = array();
        $indexProps = array_values($indexesNeed);
        $upperPrimaryKey = strtoupper(Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);
        foreach ($indexProps as $i => $indexProp) {
            $indexName = $adapter->getIndexName($tableName, $indexProp['fields'], $indexProp['type']);
            $indexProp['type'] = strtoupper($indexProp['type']);
            if ($indexProp['type'] == $upperPrimaryKey) {
                $indexKey = $upperPrimaryKey;
            } else {
                $indexKey = $indexName;
            }

            $indexProps[$i] = array(
                'KEY_NAME' => $indexName,
                'COLUMNS_LIST' => $indexProp['fields'],
                'INDEX_TYPE' => strtolower($indexProp['type'])
            );
            $indexKeys[$i] = $indexKey;
        }
        $indexesNeed = array_combine($indexKeys, $indexProps); // Array with index names as keys, except for primary

        // Foreign keys
        $foreignEntityKey = $this->getFkName($tableName, 'entity_id', 'contentmanager/content', 'entity_id');

        // Create table or modify existing one
        if (!$this->_isFlatTableExists($storeId)) {
            /** @var $table Varien_Db_Ddl_Table */
            $table = $adapter->newTable($tableName);
            foreach ($columns as $fieldName => $fieldProp) {
                
                if($fieldProp['type'] == 'text' && $fieldProp['length'] == '255') 
                {
                    $fieldProp['length'] = '64k';
                }
                
                $table->addColumn(
                    $fieldName,
                    $fieldProp['type'],
                    isset($fieldProp['length']) ? $fieldProp['length'] : null,
                    array(
                        'nullable' => isset($fieldProp['nullable']) ? (bool)$fieldProp['nullable'] : false,
                        'unsigned' => isset($fieldProp['unsigned']) ? (bool)$fieldProp['unsigned'] : false,
                        'default'  => isset($fieldProp['default']) ? $fieldProp['default'] : false,
                        'primary'  => false,
                    ),
                    isset($fieldProp['comment']) ? $fieldProp['comment'] : $fieldName
                );
            }

            foreach ($indexesNeed as $indexProp) {
                $table->addIndex($indexProp['KEY_NAME'], $indexProp['COLUMNS_LIST'],
                    array('type' => $indexProp['INDEX_TYPE']));
            }

            $table->addForeignKey($foreignEntityKey,
                'entity_id', $this->getTable('contentmanager/content'), 'entity_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

            if ($this->getFlatHelper()->isAddChildData()) {
                $table->addForeignKey($foreignChildKey,
                    'child_id', $this->getTable('contentmanager/content'), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
            }
            $table->setComment("ContentManager Content Flat (Store {$storeId})");

            $adapter->createTable($table);

            $this->_existsFlatTables[$storeId] = true;
        } else {
            $adapter->resetDdlCache($tableName);

            // Sort columns into added/altered/dropped lists
            $describe   = $adapter->describeTable($tableName);
            $addColumns     = array_diff_key($columns, $describe);
            $dropColumns    = array_diff_key($describe, $columns);
            $modifyColumns  = array();
            foreach ($columns as $field => $fieldProp) {
                if($fieldProp['type'] == 'text' && $fieldProp['length'] == '255') 
                {
                    $fieldProp['length'] = '64k';
                }
                
                if (isset($describe[$field]) && !$this->_compareColumnProperties($fieldProp, $describe[$field])) {
                    $modifyColumns[$field] = $fieldProp;
                }
            }

            // Sort indexes into added/dropped lists. Altered indexes are put into both lists.
            $addIndexes = array();
            $dropIndexes = array();
            $indexesNow  = $adapter->getIndexList($tableName); // Note: primary is always stored under 'PRIMARY' key
            $newIndexes = $indexesNeed;
            foreach ($indexesNow as $key => $indexNow) {
                if (isset($indexesNeed[$key])) {
                    $indexNeed = $indexesNeed[$key];
                    if (($indexNeed['INDEX_TYPE'] != $indexNow['INDEX_TYPE'])
                        || ($indexNeed['COLUMNS_LIST'] != $indexNow['COLUMNS_LIST'])) {
                        $dropIndexes[$key] = $indexNow;
                        $addIndexes[$key] = $indexNeed;
                    }
                    unset($newIndexes[$key]);
                } else {
                    $dropIndexes[$key] = $indexNow;
                }
            }
            $addIndexes = $addIndexes + $newIndexes;

            // Compose contstraints
            $addConstraints = array();
            $addConstraints[$foreignEntityKey] = array(
                'table_index'   => 'entity_id',
                'ref_table'     => $this->getTable('contentmanager/content'),
                'ref_index'     => 'entity_id',
                'on_update'     => Varien_Db_Ddl_Table::ACTION_CASCADE,
                'on_delete'     => Varien_Db_Ddl_Table::ACTION_CASCADE
            );

            // Additional data from childs
            $isAddChildData = $this->getFlatHelper()->isAddChildData();
            if (!$isAddChildData && isset($describe['is_child'])) {
                $adapter->delete($tableName, array('is_child = ?' => 1));
                $adapter->dropForeignKey($tableName, $foreignChildKey);
            }
            if ($isAddChildData && !isset($describe['is_child'])) {
                $adapter->delete($tableName);
                $dropIndexes['PRIMARY'] = $indexesNow['PRIMARY'];
                $addIndexes['PRIMARY']  = $indexesNeed['PRIMARY'];

                $addConstraints[$foreignChildKey] = array(
                    'table_index'   => 'child_id',
                    'ref_table'     => $this->getTable('contentmanager/content'),
                    'ref_index'     => 'entity_id',
                    'on_update'     => Varien_Db_Ddl_Table::ACTION_CASCADE,
                    'on_delete'     => Varien_Db_Ddl_Table::ACTION_CASCADE
                );
            }

            // Drop constraints
            foreach (array_keys($adapter->getForeignKeys($tableName)) as $constraintName) {
                $adapter->dropForeignKey($tableName, $constraintName);
            }

            // Drop indexes
            foreach ($dropIndexes as $indexProp) {
                $adapter->dropIndex($tableName, $indexProp['KEY_NAME']);
            }

            // Drop columns
            foreach (array_keys($dropColumns) as $columnName) {
                $adapter->dropColumn($tableName, $columnName);
            }

            // Modify columns
            foreach ($modifyColumns as $columnName => $columnProp) {
                $columnProp = array_change_key_case($columnProp, CASE_UPPER);
                if (!isset($columnProp['COMMENT'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                }
                $adapter->changeColumn($tableName, $columnName, $columnName, $columnProp);
            }

            // Add columns
            foreach ($addColumns as $columnName => $columnProp) {
                $columnProp = array_change_key_case($columnProp, CASE_UPPER);
                if (!isset($columnProp['COMMENT'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                }
                $adapter->addColumn($tableName, $columnName, $columnProp);
            }

            // Add indexes
            foreach ($addIndexes as $indexProp) {
                $adapter->addIndex($tableName, $indexProp['KEY_NAME'], $indexProp['COLUMNS_LIST'],
                    $indexProp['INDEX_TYPE']);
            }

            // Add constraints
            foreach ($addConstraints as $constraintName => $constraintProp) {
                $adapter->addForeignKey($constraintName, $tableName,
                    $constraintProp['table_index'],
                    $constraintProp['ref_table'],
                    $constraintProp['ref_index'],
                    $constraintProp['on_delete'],
                    $constraintProp['on_update']
                );
            }
        }

        $this->_preparedFlatTables[$storeId] = true;

        return $this;
    }

    /**
     * Add or Update static attributes
     *
     * @param int $storeId
     * @param int|array $contentIds update only content(s)
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function updateStaticAttributes($storeId, $contentIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter   = $this->_getWriteAdapter();
        /* @var $status Mage_Eav_Model_Entity_Attribute */
        $status    = $this->getAttribute('status');

        $fieldList  = array('entity_id');
        $colsList   = array('entity_id');
        if ($this->getFlatHelper()->isAddChildData()) {
            $fieldList = array_merge($fieldList, array('child_id', 'is_child'));
            $isChild   = new Zend_Db_Expr('0');
            $colsList  = array_merge($colsList, array('entity_id', $isChild));
        }

        $columns    = $this->getFlatColumns();
        $bind       = array(
            'store_id'       => $storeId,
            'entity_type_id' => (int)$status->getEntityTypeId(),
            'attribute_id'   => (int)$status->getId(),
            'status'         => Blackbird_ContentManager_Model_Content::STATUS_ENABLED,
            'store_admin'    => Mage_Core_Model_App::ADMIN_STORE_ID
        );

        $select     = $this->_getWriteAdapter()->select()
            ->from(array('e' => $this->getTable('contentmanager/content')), $colsList)
            ->joinLeft(
                array('t1' => $status->getBackend()->getTable()),
                'e.entity_id = t1.entity_id',
                array())
            ->where('t1.entity_type_id = :entity_type_id')
            ->where('t1.attribute_id = :attribute_id')
            ->where('t1.store_id IN (:store_id, :store_admin)')
            ->where('t1.value = :status')
            ->group('e.entity_id');
            
        foreach ($this->getAttributes() as $attributeCode => $attribute) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() == 'static') {
                if (!isset($columns[$attributeCode])) {
                    continue;
                }
                $fieldList[] = $attributeCode;
                $select->columns($attributeCode, 'e');
            }
        }

        if ($contentIds !== null) {
            $select->where('e.entity_id IN(?)', $contentIds);
        }

        $sql = $select->insertFromSelect($this->getFlatTableName($storeId), $fieldList);
        
        $adapter->query($sql, $bind);
        

        return $this;
    }

    /**
     * Remove non website contents
     *
     * @param int $storeId
     * @param int|array $contentIds
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function cleanNonWebsiteContents($storeId, $contentIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $websiteId = (int)Mage::app()->getStore($storeId)->getWebsite()->getId();
        $adapter   = $this->_getWriteAdapter();

        $joinCondition = array(
            'e.entity_id = wp.entity_id',
            'wp.store_id IN (:store_id, 0)'
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $joinCondition[] = 'e.child_id = wp.content_id';
        }
        $select = $adapter->select()
            ->from(array('e' => $this->getFlatTableName($storeId)), null);
        if ($contentIds !== null) {
            $condition = array(
                $adapter->quoteInto('e.entity_id IN(?)', $contentIds)
            );
            if ($this->getFlatHelper()->isAddChildData()) {
                $condition[] = $adapter->quoteInto('e.child_id IN(?)', $contentIds);
            }
            $select->where(implode(' OR ', $condition));
        }

        $sql = $select->deleteFromSelect('e');
        $adapter->query($sql);

        return $this;
    }

    /**
     * Update attribute flat data
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $storeId
     * @param int|array $contentIds update only content(s)
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function updateAttribute($attribute, $storeId, $contentIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter       = $this->_getWriteAdapter();
        $flatTableName = $this->getFlatTableName($storeId);
        $describe      = $adapter->describeTable($flatTableName);

        if ($attribute->getBackend()->getType() == 'static') {
            if (!isset($describe[$attribute->getAttributeCode()])) {
                return $this;
            }

            $select = $adapter->select()
                ->join(
                    array('main_table' => $this->getTable('contentmanager/content')),
                    'main_table.entity_id = e.entity_id',
                    array($attribute->getAttributeCode() => 'main_table.' . $attribute->getAttributeCode())
                );
            if ($this->getFlatHelper()->isAddChildData()) {
                $select->where('e.is_child = ?', 0);
            }
            if ($contentIds !== null) {
                $select->where('main_table.entity_id IN(?)', $contentIds);
            }

            $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
            $adapter->query($sql);
        } else {
            $columns = $attribute->getFlatColumns();
            if (!$columns) {
                return $this;
            }
            foreach (array_keys($columns) as $columnName) {
                if (!isset($describe[$columnName])) {
                    return $this;
                }
            }
            
            $select = $this->getFlatUpdateSelect($attribute, $storeId);
            
            if ($select instanceof Varien_Db_Select) {
                if ($contentIds !== null) {
                    $select->where('e.entity_id IN(?)', $contentIds);
                }
                
                
                $status    = $this->getAttribute('status');
                $select->joinLeft(
                    array('t3' => $status->getBackend()->getTable()),
                    'e.entity_id = t3.entity_id AND t3.entity_type_id = t2.entity_type_id AND t3.attribute_id = '.((int)$status->getId()).' AND t3.store_id = t2.store_id',
                    array());
                
                $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
                
                $adapter->query($sql);
            }
        }

        return $this;
    }
    

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $storeId
     * @return Varien_Db_Select
     */
    public function getFlatUpdateSelect(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $joinConditionTemplate = "%s.entity_id = %s.entity_id"
            ." AND %s.entity_type_id = ".$attribute->getEntityTypeId()
            ." AND %s.attribute_id = ".$attribute->getId()
            ." AND %s.store_id = %d";
        $joinCondition = sprintf($joinConditionTemplate,
            'e', 't1', 't1', 't1', 't1',
            Mage_Core_Model_App::ADMIN_STORE_ID);
        if ($attribute->getFlatAddChildData()) {
            $joinCondition .= ' AND e.child_id = t1.entity_id';
        }

        $valueExpr = $adapter->getCheckSql('t2.value_id > 0 AND t3.value != 0', 't2.value', 't1.value');

        /** @var $select Varien_Db_Select */
        $select = $adapter->select()
            ->joinLeft(
                array('t1' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array())
            ->joinLeft(
                array('t2' => $attribute->getBackend()->getTable()),
                sprintf($joinConditionTemplate, 'e', 't2', 't2', 't2', 't2', $storeId),
                array($attribute->getAttributeCode() => $valueExpr));
        if ($attribute->getFlatAddChildData()) {
            $select->where("e.is_child = ?", 0);
        }

        return $select;
    }    

    /**
     * Update non static EAV attributes flat data
     *
     * @param int $storeId
     * @param int|array $contentIds update only content(s)
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function updateEavAttributes($storeId, $contentIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        foreach ($this->getAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() != 'static') {
                $this->updateAttribute($attribute, $storeId, $contentIds);
            }
        }
        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $storeId
     */
    public function updateEventAttributes($storeId = null)
    {
        Mage::dispatchEvent('contentmanager_content_flat_rebuild', array(
            'store_id' => $storeId,
            'table'    => $this->getFlatTableName($storeId)
        ));
    }

    /**
     * Retrieve Content Type Instances
     * as key - type code, value - instance model
     *
     * @return array
     */
    public function getContentTypeInstances()
    {
        if ($this->_contentTypes === null) {
            $this->_contentTypes = array();
            $contentEmulator     = new Varien_Object();

            foreach (array_keys(Blackbird_ContentManager_Model_Content_Type::getTypes()) as $typeId) {
                $contentEmulator->setTypeId($typeId);
                $this->_contentTypes[$typeId] = Mage::getSingleton('contentmanager/content_type')
                    ->factory($contentEmulator);
            }
        }
        return $this->_contentTypes;
    }

    /**
     * Update relation contents
     *
     * @param int $storeId
     * @param int|array $contentIds Update child content(s) only
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function updateRelationContents($storeId, $contentIds = null)
    {
        if (!$this->getFlatHelper()->isAddChildData() || !$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        foreach ($this->getContentTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                && $relation->getTable()
                && $relation->getParentFieldName()
                && $relation->getChildFieldName()
            ) {
                $columns    = $this->getFlatColumns();
                $fieldList  = array_keys($columns);
                unset($columns['entity_id']);
                unset($columns['child_id']);
                unset($columns['is_child']);

                $select = $adapter->select()
                    ->from(
                        array('t' => $this->getTable($relation->getTable())),
                        array($relation->getParentFieldName(), $relation->getChildFieldName(), new Zend_Db_Expr('1')))
                    ->join(
                        array('e' => $this->getFlatTableName($storeId)),
                        "e.entity_id = t.{$relation->getChildFieldName()}",
                        array_keys($columns)
                    );
                if ($relation->getWhere() !== null) {
                    $select->where($relation->getWhere());
                }
                if ($contentIds !== null) {
                    $cond = array(
                        $adapter->quoteInto("{$relation->getChildFieldName()} IN(?)", $contentIds),
                        $adapter->quoteInto("{$relation->getParentFieldName()} IN(?)", $contentIds)
                    );

                    $select->where(implode(' OR ', $cond));
                }
                $sql = $select->insertFromSelect($this->getFlatTableName($storeId), $fieldList);
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Update children data from parent
     *
     * @param int $storeId
     * @param int|array $contentIds
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function updateChildrenDataFromParent($storeId, $contentIds = null)
    {
        if (!$this->getFlatHelper()->isAddChildData() || !$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        foreach (array_keys($this->getFlatColumns()) as $columnName) {
            if ($columnName == 'entity_id' || $columnName == 'child_id' || $columnName == 'is_child') {
                continue;
            }
            $select->columns(array($columnName => new Zend_Db_Expr('t1.' . $columnName)));
        }
        $select
            ->joinLeft(
                array('t1' => $this->getFlatTableName($storeId)),
                $adapter->quoteInto('t2.child_id = t1.entity_id AND t1.is_child = ?', 0),
                array())
            ->where('t2.is_child = ?', 1);

        if ($contentIds !== null) {
            $select->where('t2.child_id IN(?)', $contentIds);
        }

        $sql = $select->crossUpdateFromSelect(array('t2' => $this->getFlatTableName($storeId)));
        $adapter->query($sql);

        return $this;
    }

    /**
     * Clean unused relation contents
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function cleanRelationContents($storeId)
    {
        if (!$this->getFlatHelper()->isAddChildData()) {
            return $this;
        }

        foreach ($this->getContentTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $adapter  = $this->_getWriteAdapter();
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                && $relation->getTable()
                && $relation->getParentFieldName()
                && $relation->getChildFieldName()
            ) {
                $select = $this->_getWriteAdapter()->select()
                    ->distinct(true)
                    ->from(
                        $this->getTable($relation->getTable()),
                        "{$relation->getParentFieldName()}"
                    );
                $joinLeftCond = array(
                    "e.entity_id = t.{$relation->getParentFieldName()}",
                    "e.child_id = t.{$relation->getChildFieldName()}"
                );
                if ($relation->getWhere() !== null) {
                    $select->where($relation->getWhere());
                    $joinLeftCond[] = $relation->getWhere();
                }

                $entitySelect = new Zend_Db_Expr($select->__toString());

                $select = $adapter->select()
                    ->from(array('e' => $this->getFlatTableName($storeId)), null)
                    ->joinLeft(
                        array('t' => $this->getTable($relation->getTable())),
                        implode(' AND ', $joinLeftCond),
                        array())
                    ->where('e.is_child = ?', 1)
                    ->where('e.entity_id IN(?)', $entitySelect)
                    ->where("t.{$relation->getChildFieldName()} IS NULL");

                $sql = $select->deleteFromSelect('e');
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Remove content data from flat
     *
     * @param int|array $contentIds
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function removeContent($contentIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $cond = array(
            $adapter->quoteInto('entity_id IN(?)', $contentIds)
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $cond[] = $adapter->quoteInto('child_id IN(?)', $contentIds);
        }
        $cond = implode(' OR ', $cond);
        $adapter->delete($this->getFlatTableName($storeId), $cond);

        return $this;
    }

    /**
     * Remove children from parent content
     *
     * @param int|array $contentIds
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function removeContentChildren($contentIds, $storeId)
    {
        if (!$this->getFlatHelper()->isAddChildData()) {
            return $this;
        }
        $whereExpr = array(
            'entity_id IN(?)' => $contentIds,
            'is_child = ?'    => 1
        );
        $this->_getWriteAdapter()->delete($this->getFlatTableName($storeId), $whereExpr);

        return $this;
    }

    /**
     * Update flat data for content
     *
     * @param int|array $contentIds
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function updateContent($contentIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $this->saveContent($contentIds, $storeId);

        Mage::dispatchEvent('contentmanager_content_flat_update_content', array(
            'store_id'      => $storeId,
            'table'         => $this->getFlatTableName($storeId),
            'content_ids'   => $contentIds
        ));

        return $this;
    }

    /**
     * Save content(s) data for store
     *
     * @param int|array $contentIds
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function saveContent($contentIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $this->updateStaticAttributes($storeId, $contentIds);
        $this->updateEavAttributes($storeId, $contentIds);

        return $this;
    }

    /**
     * Delete flat table process
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function deleteFlatTable($storeId)
    {
        if ($this->_isFlatTableExists($storeId)) {
            $this->_getWriteAdapter()->dropTable($this->getFlatTableName($storeId));
        }

        return $this;
    }

    /**
     * Check is flat table for store exists
     *
     * @param int $storeId
     * @return bool
     */
    protected function _isFlatTableExists($storeId)
    {
        if (!isset($this->_existsFlatTables[$storeId])) {
            $tableName     = $this->getFlatTableName($storeId);
            $isTableExists = $this->_getWriteAdapter()->isTableExists($tableName);

            $this->_existsFlatTables[$storeId] = $isTableExists ? true : false;
        }

        return $this->_existsFlatTables[$storeId];
    }

    /**
     * Retrieve previous key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    protected function _arrayPrevKey(array $array, $key)
    {
        $prev = false;
        foreach (array_keys($array) as $k) {
            if ($k == $key) {
                return $prev;
            }
            $prev = $k;
        }
        return false;
    }

    /**
     * Retrieve next key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    protected function _arrayNextKey(array $array, $key)
    {
        $next = false;
        foreach (array_keys($array) as $k) {
            if ($next === true) {
                return $k;
            }
            if ($k == $key) {
                $next = true;
            }
        }
        return false;
    }

    /**
     * Transactional rebuild ContentManager Content Flat Data
     *
     * @return Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer
     */
    public function reindexAll()
    {
        foreach (Mage::app()->getStores() as $storeId => $store) {
            $this->prepareFlatTable($storeId);
            $this->beginTransaction();
            try {
                $this->rebuild($store);
                $this->commit();
           } catch (Exception $e) {
                $this->rollBack();
                throw $e;
           }
        }

        return $this;
    }
}
