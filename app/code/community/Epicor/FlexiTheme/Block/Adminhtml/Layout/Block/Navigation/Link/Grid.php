<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Link_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('layoutblocknavlinkgrid');
        $this->setDefaultSort('display_title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('flexitheme/layout_block_link')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('display_title', array(
            'header' => Mage::helper('flexitheme')->__('Display Title'),
            'align' => 'left',
            'index' => 'display_title',
        ));

        $this->addColumn('tooltip_title', array(
            'header' => Mage::helper('flexitheme')->__('ToolTip Title'),
            'align' => 'left',
            'index' => 'tooltip_title',
        ));

        $this->addColumn('link_url', array(
            'header' => Mage::helper('flexitheme')->__('Custom Url'),
            'align' => 'left',
            'index' => 'link_url',
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
                    'confirm' => Mage::helper('flexitheme')->__('Are you sure you want to delete this navigation link? This action cannot be undone')
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