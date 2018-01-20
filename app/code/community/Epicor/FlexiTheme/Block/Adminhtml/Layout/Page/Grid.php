<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('layoutpagegrid');
        $this->setDefaultSort('page_name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('flexitheme/layout_page')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('page_name', array(
            'header' => Mage::helper('flexitheme')->__('Page Name'),
            'align' => 'left',
            'index' => 'page_name',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('flexitheme')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('flexitheme')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => Mage::helper('flexitheme')->__('Are you sure you want to delete this page? This action cannot be undone')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}