<?php
/**
 * Block class for Ship status  mapping grid
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_Block_Adminhtml_Mapping_Shipstatus_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter {

    public function __construct() {
        parent::__construct();
        $this->setId('shipstatusmappingGrid');
        $this->setDefaultSort('code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('customerconnect/erp_mapping_shipstatus')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('code', array(
            'header' => Mage::helper('customerconnect')->__('Ship Status Code'),
            'align' => 'left',
            'index' => 'code',
        ));
        $this->addColumn('description', array(
            'header' => Mage::helper('customerconnect')->__('Ship Status Description'),
            'align' => 'left',
            'index' => 'description'
        ));
        $this->addColumn('status_help', array(
            'header' => Mage::helper('customerconnect')->__('Status Help'),
            'align' => 'left',
            'index' => 'status_help',
            'width' => "25px",
            'renderer' => 'customerconnect/adminhtml_mapping_shipstatus_renderer_textarea',
        ));
        $this->addColumn('is_default', array(
            'header' => Mage::helper('customerconnect')->__('Default'),
            'width' => '20px',
            'type' => 'checkbox',
            'align' => 'center',
            'index' => 'is_default',
            //'values'   => array(1),
            'filter' => false,
            'renderer' => 'customerconnect/adminhtml_mapping_shipstatus_renderer_checkbox',
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
                    'field' => 'id',
                    'confirm' => Mage::helper('epicor_comm')->__('Delete selected items?')
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
        //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
