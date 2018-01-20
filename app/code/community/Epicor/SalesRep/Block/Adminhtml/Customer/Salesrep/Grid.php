<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('salesrep_grid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_salesrep/account')->getCollection();
        /* @var $collection Epicor_SalesRep_Model_Resource_Account_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('accounts');

        $groups = $this->helper('customer')->getGroups()->toOptionArray();

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_salesrep')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_salesrep')->__('Delete selected Sales Rep Accounts?')
        ));

        return $this;
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();



        $this->addColumn('sales_rep_id', array(
            'header' => Mage::helper('epicor_salesrep')->__('Sales Rep Id'),
            'index' => 'sales_rep_id',
            'width' => '20px',
            'filter_index' => 'sales_rep_id'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_salesrep')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('epicor_salesrep')->__('Created'),
            'index' => 'created_at',
            'width' => '200px',
            'filter_index' => 'created_at',
            'type' => 'datetime',
        ));

        $this->addColumn('updated_at', array(
            'header' => Mage::helper('epicor_salesrep')->__('Last Updated'),
            'index' => 'updated_at',
            'width' => '200px',
            'type' => 'datetime',
            'filter_index' => 'updated_at',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_salesrep')->__('Action'),
            'width' => '100',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_salesrep')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_salesrep')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => Mage::helper('epicor_salesrep')->__('Are you sure you want to delete this sales rep? This action cannot be undone')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_salesrep')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('epicor_salesrep')->__('XML'));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
