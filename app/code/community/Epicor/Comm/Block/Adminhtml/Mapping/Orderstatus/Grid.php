<?php

class Epicor_Comm_Block_Adminhtml_Mapping_Orderstatus_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter {

    public function __construct() {
        parent::__construct();
        $this->setId('orderstatusmappingGrid');
        $this->setDefaultSort('code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
   //     $collection = Mage::getModel('epicor_comm/erp_mapping_orderstatus');
        $collection = Mage::getModel('epicor_comm/erp_mapping_orderstatus')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('code', array(
            'header' => Mage::helper('epicor_comm')->__('Erp Code'),
            'align' => 'left',
            'index' => 'code',
            'width' => '50px'
        ));

//      $this->addColumn('description', array(
//          'header'    => Mage::helper('epicor_comm')->__('Description'),
//          'align'     => 'left',
//          'index'     => 'description',
//      ));

//        $this->addColumn('state', array(
//            'header' => Mage::helper('epicor_comm')->__('Order State'),
//            'align' => 'left',
//            'index' => 'state',
//            'width' => '50px',
//            'column_css_class'=> 'no-display',
//            'header_css_class'=> 'no-display',
//        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('epicor_comm')->__('Order Status'),
            'align' => 'left',
            'index' => 'status',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Mapping_Orderstatus_Renderer_Status,
            'width' => '50px',
        ));
        $this->addColumn('sou_trigger', array(
            'header' => Mage::helper('epicor_comm')->__('Sou Trigger'),
            'align' => 'left',
            'index' => 'sou_trigger',
            'width' => '50px'
        ));
        
        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}