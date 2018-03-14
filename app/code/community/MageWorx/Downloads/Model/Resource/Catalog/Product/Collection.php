<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Catalog_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    public function getSize()
    {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $data = $this->_fetchAll($select);
        return count($data);
    }
}