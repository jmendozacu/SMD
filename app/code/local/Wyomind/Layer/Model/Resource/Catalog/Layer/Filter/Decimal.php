<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_Resource_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Resource_Layer_Filter_Decimal
{
    /**
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @param float $range
     * @param int $index
     * @return $this
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        $from = $range;
        $to = $index;
        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId())
        );

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            implode(' AND ', $conditions),
            array()
        );

        if ($from) {
            $collection->getSelect()->where("{$tableAlias}.value >= ?", (float) $from);
        }
        if ($to) {
            $collection->getSelect()->where("{$tableAlias}.value <= ?", (float) $to);
        }

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Layer_Filter_Abstract $filter
     * @param Varien_Data_Collection_Db $collection
     * @param array $productIds
     * @return array
     */
    public function getMinMaxValues($filter, $collection, $productIds = array())
    {
        $select = clone $collection->getSelect();
        $select->reset(Zend_Db_Select::WHERE);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::ORDER);
        $tableAlias = 'at_' . $filter->getAttributeModel()->getAttributeCode();
        $select->columns(
            array(
                'min' => new Zend_Db_Expr('MIN('. $tableAlias .'.value)'),
                'max' => new Zend_Db_Expr('MAX('. $tableAlias .'.value)'),
            )
        );

        if (!empty($productIds)) {
            $select->where('e.entity_id IN (?)', $productIds);
        }
        $row = $this->_getReadAdapter()->fetchRow($select);

        return array(floor($row['min']),  round($row['max']));
    }
}