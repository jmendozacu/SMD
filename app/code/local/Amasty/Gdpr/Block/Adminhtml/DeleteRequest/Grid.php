<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_DeleteRequest_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('amgdprDeleteRequestGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        /** @var Amasty_Gdpr_Model_Resource_DeleteRequest_Collection $collection */
        $collection = Mage::getResourceModel('amgdpr/deleteRequest_collection');

        $collection->joinOrderStats();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'created_at',
            array(
                'index' => 'created_at',
                'filter_index' => 'main_table.created_at',
                'header' => $this->__('Date Submitted'),
                'type' => 'datetime'
            )
        );

        $this->addColumn(
            'customer_name',
            array(
                'header' => $this->__('Name'),
                'index' => 'customer_name',
            )
        );

        $this->addColumn(
            'customer_email',
            array(
                'header' => $this->__('Email'),
                'index' => 'customer_email',
                'filter_index' => 'main_table.customer_email',
            )
        );

        $this->addColumn(
            'complete_qty',
            array(
                'header' => $this->__('Completed Orders Qty'),
                'index' => 'complete_qty',
                'filter' => false,
                'sortable' => false,
            )
        );

        $this->addColumn(
            'pending_qty',
            array(
                'header' => $this->__('Pending Orders Qty'),
                'index' => 'pending_qty',
                'filter' => false,
                'sortable' => false,
            )
        );

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()
            ->addItem(
                'approve',
                array(
                    'label' => $this->__('Approve Delete Request'),
                    'url' => $this->getUrl('*/*/approve'),
                    'confirm' => $this->__('Are you sure?')
                )
            )
            ->addItem(
                'deny',
                array(
                    'label' => $this->__('Deny Delete Request'),
                    'url' => $this->getUrl('*/*/deny'),
                    'confirm' => $this->__('Are you sure?')
                )
            );

        return $this;
    }

    public function getRowUrl($row)
    {
        return 'javascript:void(0)';
    }
}