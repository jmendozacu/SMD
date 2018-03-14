<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Categories_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/categories');
    }

    public function addStatusFilter()
    {
        $this->getSelect()->where('main_table.is_active = ?', MageWorx_Downloads_Helper_Data::STATUS_ENABLED);
        return $this;
    }

    public function addSortOrder()
    {
        $this->getSelect()->order($this->getIdFieldName());
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
}
