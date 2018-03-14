<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_Downloads_Block_Adminhtml_Files_Edit_Tab_Downloads extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_tree = array();

    public function __construct()
    {
        parent::__construct();

        $this->setId('downloadsHistoryGrid');
        $this->setDefaultSort('full_name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('downloads_filter');
    }

    public function getFileId()
    {
        return (int)Mage::app()->getFrontController()->getRequest()->getParam('id');
    }

    protected function _getHelper()
    {
        return Mage::helper('mageworx_downloads');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageworx_downloads/history')
            ->getCustomerHistoryCollection()
            ->addFileIdFilter($this->getFileId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = $this->_getHelper();
        $this->addExportType('*/*/exportDownloadsCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportDownloadsXml', Mage::helper('sales')->__('XML'));

        $this->addColumn(
            'full_name', array(
            'header' => $helper->__('Name'),
            'index'  => 'full_name',
            'type'   => 'text',
            )
        );

        $this->addColumn(
            'email', array(
            'header' => $helper->__('Email'),
            'type'   => 'text',
            'index'  => 'email',
            )
        );

        $this->addColumn(
            'first_download_date', array(
            'header' => $helper->__('First Download Date'),
            'width'  => 150,
            'type'   => 'datetime',
            'index'  => 'first_download_date',
            )
        );

        $this->addColumn(
            'last_download_date', array(
            'header' => $helper->__('Recent Download Date'),
            'width'  => 150,
            'type'   => 'datetime',
            'index'  => 'last_download_date',
            )
        );

        $this->addColumn(
            'downloads_count', array(
            'header'                    => $helper->__('Number of Downloads'),
            'width'                     => 100,
            'align'                     => 'right',
            'index'                     => 'downloads_count',
            'filter'                    => false,
            'filter_condition_callback' => array($this, '_downloadsCountCallback')
            )
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/downloadsGrid', array('_current' => true));
    }

    protected function _downloadsCountCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection = $collection->getSelect();

        if (isset($value['from'])) {
            $collection->having("downloads_count >= ?", $value['from']);
        }
        if (isset($value['to'])) {
            $collection->having("downloads_count <= ?", $value['to']);
        }

        return $this;
    }
}
