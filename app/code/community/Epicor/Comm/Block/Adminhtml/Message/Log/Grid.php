<?php

class Epicor_Comm_Block_Adminhtml_Message_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('log_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/message_log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => Mage::helper('epicor_comm')->__('ID'),
            'align' => 'left',
            'index' => 'id',
            'type' => 'range'
        ));

        $this->addColumn('message_parent', array(
            'header' => Mage::helper('epicor_comm')->__('Parent'),
            'align' => 'left',
            'index' => 'message_parent',
            'type' => 'options',
            'options' => Mage::getModel('epicor_comm/message_log')->getMessageParents(),
        ));

        $this->addColumn('store', array(
            'header' => Mage::helper('epicor_comm')->__('Store'),
            'align' => 'left',
            'index' => 'store',
            'width' => '100',
        ));

        $this->addColumn('message_type', array(
            'header' => Mage::helper('epicor_comm')->__('Type'),
            'align' => 'left',
            'index' => 'message_type',
            'renderer' => new Epicor_Comm_Block_Renderer_Message(),
            'width' => '120',
        ));

        $this->addColumn('message_status', array(
            'header' => Mage::helper('epicor_comm')->__('Status'),
            'align' => 'left',
            'type' => 'options',
            'index' => 'message_status',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Messagestatus(),
            'options' => Mage::getModel('epicor_comm/message_log')->getMessageStatuses(),
        ));

        $this->addColumn('cached', array(
            'header' => Mage::helper('epicor_comm')->__('Cached'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'cached',
        ));

        $this->addColumn('message_subject', array(
            'header' => Mage::helper('epicor_comm')->__('Subject'),
            'align' => 'left',
            'index' => 'message_subject'
        ));

        $this->addColumn('message_secondary_subject', array(
            'header' => Mage::helper('epicor_comm')->__('2nd Subject'),
            'align' => 'left',
            'index' => 'message_secondary_subject',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Logsubject(),
            'width' => '150px'
        ));

        $this->addColumn('status_code', array(
            'header' => Mage::helper('epicor_comm')->__('Code'),
            'align' => 'left',
            'index' => 'status_code',
            'width' => '40',
        ));

        $this->addColumn('status_description', array(
            'header' => Mage::helper('epicor_comm')->__('Description'),
            'width' => '150',
            'type' => 'text',
            'align' => 'left',
            'index' => 'status_description'
        ));

        $this->addColumn('start_datestamp', array(
            'header' => Mage::helper('epicor_comm')->__('Start Time'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'start_datestamp',
        ));

        $this->addColumn('duration', array(
            'header' => Mage::helper('epicor_comm')->__('Duration (ms)'),
            'align' => 'left',
            'index' => 'duration',
            'width' => '40',
            'type' => 'range'
        ));

        $this->addColumn('url', array(
            'header' => Mage::helper('epicor_comm')->__('Url'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'url',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Logurl(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('View'),
                    'url' => array('base' => '*/*/view'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Reprocess'),
                    'url' => array('base' => '*/*/reprocess'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => Mage::helper('epicor_comm')->__('Are you sure you want to delete this log entry? This action cannot be undone')
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

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('logid');

        $this->getMassactionBlock()->addItem('reprocess', array(
            'label' => Mage::helper('epicor_comm')->__('Reprocess'),
            'url' => $this->getUrl('*/*/massReprocess'),
            'confirm' => Mage::helper('epicor_comm')->__('Reprocess selected messages?')
        ));

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_comm')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_comm')->__('Delete selected messages?')
        ));

        return $this;
    }

}
