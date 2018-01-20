<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_Resource_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{
    /**
     * @param Varien_Data_Collection_Db $collection
     * @param array $productIds
     * @return array
     */
    public function getMinMaxPrices($collection, $productIds = array())
    {
        $select = clone $collection->getSelect();
        $select->reset(Zend_Db_Select::WHERE);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::GROUP);
        $select->columns(
            array(
                'min' => new Zend_Db_Expr('MIN(price_index.min_price)'),
                'max' => new Zend_Db_Expr('MAX(price_index.max_price)'),
            )
        );

        if (!empty($productIds)) {
            $select->where('e.entity_id IN (?)', $productIds);
        }

        $row = $this->_getReadAdapter()->fetchRow($select);

        return array(floor($row['min']),  round($row['max']));
    }
}