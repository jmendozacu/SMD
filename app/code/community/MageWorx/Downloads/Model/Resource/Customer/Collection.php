<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Customer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/customer');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinRight(
                array('pd' => $this->getTable('catalog/product_flat').'_1'),
                'main_table.product_id = pd.entity_id',
                array('product_name' => 'name')
            )
            ->joinRight(
                array('df' => $this->getTable('mageworx_downloads/files')),
                'main_table.file_id = df.file_id',
                array('file_name' => 'name')
            );

        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('main_table.customer_id = ?', $customerId);
        return $this;
    }
}