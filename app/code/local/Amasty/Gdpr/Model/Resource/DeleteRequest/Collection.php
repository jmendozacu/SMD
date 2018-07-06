<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Resource_DeleteRequest_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('amgdpr/deleteRequest');
    }

    public function deleteByCustomerId($customerId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            array('customer_id = ?' => $customerId)
        );
    }

    public function joinOrderStats()
    {
        $this
            ->joinOrderStatuses(array('complete'))
            ->joinOrderStatuses(array('pending', 'pending_payment'));

        $this->getSelect()->group('main_table.id');
    }

    protected function joinOrderStatuses($statuses)
    {
        $alias = $statuses[0];

        $joinCondition = $this->getConnection()->quoteInto(
            "{$alias}_order.customer_id = main_table.customer_id AND {$alias}_order.status IN (?)",
            $statuses
        );

        $this->getSelect()
            ->joinLeft(
                array("{$alias}_order" => $this->getTable('sales/order')),
                $joinCondition,
                array("{$alias}_qty" => "COUNT(DISTINCT {$alias}_order.entity_id)")
            );

        return $this;
    }
}
