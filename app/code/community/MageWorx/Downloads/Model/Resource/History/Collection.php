<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/history');
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $unionSelect = clone $this->getSelect();

        $countSelect = clone $this->getSelect();
        $countSelect->reset();
        $countSelect->from($unionSelect, 'COUNT(*)');

        return $countSelect;
    }

    /**
     * Get file downloads history
     * @return $this
     */
    public function getDownloadsHistory()
    {
        $eavConfig = Mage::getSingleton('eav/config');
        $firstnameAttId = $eavConfig->getAttribute('customer', 'firstname')->getData('attribute_id');
        $lastnameAttId  = $eavConfig->getAttribute('customer', 'lastname')->getData('attribute_id');

        $customerEntityVarchar = Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar');
        $customerEntity = Mage::getSingleton('core/resource')->getTableName('customer_entity');

        $this->getSelect()
            ->columns(array('last_download_date' => 'MAX(download_date)'), 'main_table')
            ->columns(array('first_download_date' => 'MIN(download_date)'), 'main_table')
            ->joinLeft(
                array('cev' => $customerEntityVarchar),
                'main_table.customer_id = cev.entity_id AND cev.attribute_id = ' . $firstnameAttId,
                array('first_name' => 'value')
            )
            ->joinLeft(
                array('cev2' => $customerEntityVarchar),
                'main_table.customer_id = cev2.entity_id AND cev2.attribute_id = ' . $lastnameAttId,
                array('last_name' => 'value')
            )
            ->joinLeft(
                array('ce' => $customerEntity),
                'main_table.customer_id = ce.entity_id',
                array('email' => 'email')
            )
            ->columns(new Zend_Db_Expr("CONCAT(cev.value, ' ', cev2.value) AS full_name"))
            ->columns(new Zend_Db_Expr("SUM(main_table.total_downloads) AS downloads_count"))
            ->group('customer_id');

        return $this;
    }

    /**
     * Filter collection by customers
     * @return $this
     */
    public function filterCustomers()
    {
        return $this->addFieldToFilter('main_table.customer_id', array('neq' => 0));
    }

    /**
     * Filter collection by guests
     * @return $this
     */
    public function filterGuests()
    {
        return $this->addFieldToFilter('main_table.customer_id', array('eq' => 0));
    }

    /**
     * @param int $fileId
     * @return bool
     */
    public function updateDownloadsCount($fileId)
    {
        $guest = $this->getLastGuest($fileId);
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        if ($guest) {
            $connection->update(
                $this->getMainTable(),
                array('total_downloads' => $guest['total_downloads'] + 1),
                array('id = ?' => $guest['id'] )
            );

            return true;
        }
        return false;
    }

    protected function getLastGuest($fileId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $connection->select()
            ->from($this->getMainTable(), array('id', 'total_downloads'))
            ->where('customer_id = ?', 0)
            ->where('file_id = ?', $fileId)
            ->order(array('id DESC'))
            ->limit(1);
        $lastGuest = $connection->fetchRow($select);

        return $lastGuest;
    }

    /**
     * Add filter by file ID
     * @param string $id
     * @return $this
     */
    public function addFileIdFilter($id)
    {
        $this->addFieldToFilter('main_table.file_id', array('eq' => $id));
        return $this;
    }
}