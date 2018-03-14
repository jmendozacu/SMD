<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Categories_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('categoriesGrid');
        $this->setDefaultSort('title');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageworx_downloads/categories')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function getStoreId()
    {
        return Mage::registry('store_id');
    }

    private function _getHelper()
    {
        return Mage::helper('mageworx_downloads');
    }

    protected function _prepareColumns()
    {
        $helper = $this->_getHelper();

        $this->addColumn(
            'category_id', array(
            'header' => $helper->__('ID'),
            'index' => 'category_id',
            'type' => 'number',
            )
        );

        $this->addColumn(
            'title', array(
            'header' => $helper->__('Name'),
            'width' => 300,
            'index' => 'title'
            )
        );

        $this->addColumn(
            'description', array(
            'header' => $helper->__('Short Description'),
            'index' => 'description'
            )
        );

        $this->addColumn(
            'files', array(
            'header' => $helper->__('Files'),
            'width' => 100,
            'sortable' => false,
            'filter' => false,
            'renderer' => 'mageworx_downloads/adminhtml_categories_grid_renderer_files'
            )
        );

        $this->addColumn(
            'is_active', array(
            'header' => $helper->__('Status'),
            'width' => 80,
            'index' => 'is_active',
            'type' => 'options',
            'options' => $helper->getStatusArray()
            )
        );

        $this->addColumn(
            'actions',
            array(
                'header' => $helper->__('Action'),
                'index' => 'stores',
                'width' => 70,
                'renderer' => 'mageworx_downloads/adminhtml_categories_grid_renderer_action',
                'sortable' => false,
                'filter' => false,
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _prepareMassaction()
    {
        $helper = $this->_getHelper();
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('categories');

        $confirm = $helper->__('All files inside will be moved to Default Category. Are you sure you want to proceed?');

        $this->getMassactionBlock()->addItem(
            'delete', array(
            'label' => $helper->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete', array('store' => $this->getStoreId())),
            'confirm' => $confirm
            )
        );

        $status = $helper->getStatusArray();
        array_unshift($status, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem(
            'is_active', array(
            'label' => $helper->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name' => 'is_active',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $helper->__('Status'),
                    'values' => $status
                )
            )
            )
        );
        return $this;
    }

    public function getRowUrl($row)
    {
        if ($row->getId() > 1) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $this->getStoreId()));
        }
    }
}
