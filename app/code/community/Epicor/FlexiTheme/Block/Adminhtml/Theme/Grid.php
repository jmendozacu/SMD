<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('themegrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('flexitheme/theme')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('theme_name', array(
            'header' => Mage::helper('flexitheme')->__('Theme Name'),
            'align' => 'left',
            'index' => 'theme_name',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('flexitheme')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('flexitheme')->__('Activate/Deactivate'),
            #        'url' => array('base' => '*/*/activate'),
                     'url' => array('base' => 'adminhtml/flexitheme_scope/index',array('previousUrlController' =>$currentUrlController)),
                    'field' => 'id',
                ),
                array(
                    'caption' => Mage::helper('flexitheme')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => Mage::helper('flexitheme')->__('Are you sure you want to delete this skin? This action cannot be undone')
                ),
                array(
                    'caption' => Mage::helper('flexitheme')->__('Export'),
                    'url' => array('base' => '*/*/export'),
                    'field' => 'id'
                ),
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