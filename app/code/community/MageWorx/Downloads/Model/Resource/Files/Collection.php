<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Files_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/files');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $modelCat = Mage::getResourceSingleton('mageworx_downloads/categories');
        $this->getSelect()
            ->joinLeft(
                array('relation' => $this->getTable('mageworx_downloads/relation')),
                'main_table.file_id = relation.file_id',
                array('products_count' => 'COUNT(relation.product_id)')
            )
            ->joinLeft(
                array('cat' => $modelCat->getMainTable()),
                'main_table.category_id = cat.category_id',
                array('title')
            )
            ->group('main_table.file_id');

        return $this;
    }

    public function addStatusFilter()
    {
        $this->getSelect()->where('main_table.is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        return $this;
    }

    public function addAttachFilter()
    {
        $values = array(MageWorx_Downloads_Helper_Data::ATTACH_ENABLED);

        if (Mage::helper('mageworx_downloads')->isEnabledInEmails()) {
            $values[] = MageWorx_Downloads_Helper_Data::ATTACH_USE_CONFIG;
        }

        $this->getSelect()->where('main_table.is_attach IN (?)', $values);
        return $this;
    }

    public function addCategoryStatusFilter()
    {
        $this->getSelect()->where('cat.is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        return $this;
    }

    public function addCategoryFilter($ids)
    {
        $this->getSelect()
            ->where('main_table.category_id IN (?)', $ids);
        return $this;
    }

    public function addSortOrder($sort = '')
    {
        if (empty($sort)) {
            $sort = Mage::helper('mageworx_downloads')->getSortOrder();
        }
        switch ($sort) {
            case 1:
                $order = 'name asc';
                break;
            case 2:
                $order = 'date_added desc';
                break;
            case 3:
                $order = 'size desc';
                break;
            case 4:
                $order = 'downloads desc';
                break;
        }
        $this->getSelect()->order($order);
        return $this;
    }

    public function addResetFilter()
    {
        $this->getSelect()->reset('where');
        return $this;
    }

    public function addStoreFilter()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            $this->getSelect()->where('main_table.store_ids like "%,' . $this->getStoreId() . '" or main_table.store_ids like "' . $this->getStoreId() . ',%" or main_table.store_ids like "%,' . $this->getStoreId() . ',%" or main_table.store_ids = ' . $this->getStoreId() . ' or main_table.store_ids = 0');
        }
        return $this;
    }

    public function addFilesFilter(array $ids)
    {
        $this->getSelect()->where('main_table.file_id IN (?)', $ids);
        return $this;
    }

    protected function getStoreId()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::registry('store_id');
        } else {
            return Mage::app()->getStore()->getId();
        }
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $totalSelect = clone $countSelect;
        $totalSelect->reset();
        $totalSelect->from(array('t1' => $countSelect), array('COUNT(*)'));

        return $totalSelect;
    }
}
