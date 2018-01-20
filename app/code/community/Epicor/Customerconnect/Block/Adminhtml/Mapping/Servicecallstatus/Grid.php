<?php

class Epicor_Customerconnect_Block_Adminhtml_Mapping_Servicecallstatus_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter {

    public function __construct() {
        parent::__construct();
        $this->setId('servicecallstatusmappingGrid');
        $this->setDefaultSort('code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('customerconnect/erp_mapping_servicecallstatus')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('code', array(
            'header' => Mage::helper('customerconnect')->__('Service Call Status Code'),
            'align' => 'left',
            'index' => 'code',
            'width' => '50px'
        ));

//        $this->addColumn('state', array(
//            'header' => Mage::helper('customerconnect')->__('Service Call State'),
//            'align' => 'left',
//            'index' => 'state',
//            'width' => '50px'
//        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('customerconnect')->__('Service Call Status'),
            'align' => 'left',
            'index' => 'status',
            'width' => '50px'
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