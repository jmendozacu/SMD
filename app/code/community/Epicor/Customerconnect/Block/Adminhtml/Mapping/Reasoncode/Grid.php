<?php

class Epicor_Customerconnect_Block_Adminhtml_Mapping_Reasoncode_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter {
    public function __construct() {
        parent::__construct();
        $this->setId('reasoncodemappingGrid');
        $this->setDefaultSort('code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('customerconnect/erp_mapping_reasoncode')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('code', array(
            'header' => Mage::helper('customerconnect')->__('Reason Code'),
            'align' => 'left',
            'index' => 'code',
        ));
        $this->addColumn('description', array(
            'header' => Mage::helper('customerconnect')->__('Reason Code Description'),
            'align' => 'left',
            'index' => 'description'
        ));
        $this->addColumn('type', array(
            'header'    => Mage::helper('customerconnect')->__('Reason Code Type'),
            'align'     => 'left',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Mage::getModel('customerconnect/erp_mapping_reasoncodetypes')->toArray(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('customerconnect')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('customerconnect')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('customerconnect')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customerconnect')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customerconnect')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}