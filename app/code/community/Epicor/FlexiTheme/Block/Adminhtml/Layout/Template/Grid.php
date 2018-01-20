<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('layouttemplategrid');
        $this->setDefaultSort('template_name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('flexitheme/layout_template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('template_name', array(
            'header' => Mage::helper('flexitheme')->__('Template Name'),
            'align' => 'left',
            'index' => 'template_name',
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
                    'confirm' => Mage::helper('flexitheme')->__('Are you sure you want to delete this template? This action cannot be undone')
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