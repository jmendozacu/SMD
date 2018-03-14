<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Customer_Edit_Tab_Files_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('files_grid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getCustomerId() instanceof Mage_Customer_Model_Customer) {
            $this->setCustomerId($this->getCustomerId()->getId());
        }

        $collection = Mage::getResourceModel('mageworx_downloads/customer_collection')
            ->addCustomerFilter($this->getCustomerId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_id', array(
            'header'    => Mage::helper('mageworx_downloads')->__('Product Id'),
            'width'     => '90px',
            'index'     => 'product_id',
            )
        );

        $this->addColumn(
            'product_name', array(
            'header'    => Mage::helper('mageworx_downloads')->__('Product Name'),
            'index'     => 'product_name',
            )
        );

        $this->addColumn(
            'file_id', array(
            'header'    => Mage::helper('mageworx_downloads')->__('File Id'),
            'width'     => '90px',
            'index'     => 'file_id',
            'filter'    => false,
            )
        );

        $this->addColumn(
            'file_name', array(
            'header'    => Mage::helper('mageworx_downloads')->__('File Name'),
            'index'     => 'file_name',
            'filter'    => false,
            )
        );

        $this->addColumn(
            'download_date', array(
            'header'    => Mage::helper('mageworx_downloads')->__('Download Date'),
            'index'     => 'download_date',
            'filter'    => false,
            )
        );

        return parent::_prepareColumns();
    }
}