<?php

class Epicor_Comm_Block_Adminhtml_Locations_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('locations_grid');
        $this->setDefaultSort('code');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/location')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $columns = array(
            'erp_code' => array(
                'header' => Mage::helper('epicor_comm')->__('ERP Code'),
                'align' => 'left',
                'index' => 'code',
                'type' => 'text'
            ),
            'name' => array(
                'header' => Mage::helper('epicor_comm')->__('Name'),
                'align' => 'left',
                'index' => 'name',
                'type' => 'text'
            ),
            'address1' => array(
                'header' => Mage::helper('epicor_comm')->__('Address 1'),
                'align' => 'left',
                'index' => 'address1',
                'type' => 'text'
            ),
            'address2' => array(
                'header' => Mage::helper('epicor_comm')->__('Address 2'),
                'align' => 'left',
                'index' => 'address2',
                'type' => 'text'
            ),
            'address3' => array(
                'header' => Mage::helper('epicor_comm')->__('Address 3'),
                'align' => 'left',
                'index' => 'address3',
                'type' => 'text'
            ),
            'city' => array(
                'header' => Mage::helper('epicor_comm')->__('City'),
                'align' => 'left',
                'index' => 'city',
                'type' => 'text'
            ),
            'county' => array(
                'header' => Mage::helper('epicor_comm')->__('County'),
                'align' => 'left',
                'index' => 'county',
                'type' => 'state'
            ),
            'country' => array(
                'header' => Mage::helper('epicor_comm')->__('Country'),
                'align' => 'left',
                'index' => 'country',
                'type' => 'country'
            ),
            'postcode' => array(
                'header' => Mage::helper('epicor_comm')->__('Postcode'),
                'align' => 'left',
                'index' => 'postcode',
                'type' => 'text'
            ),
            'sort_order' => array(
                'header' => Mage::helper('epicor_comm')->__('Sort Order'),
                'align' => 'left',
                'index' => 'sort_order',
                'type' => 'text'
            ),
            'telephone_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Telephone Number'),
                'align' => 'left',
                'index' => 'telephone_number',
                'type' => 'text'
            ),
            'fax_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Fax Number'),
                'align' => 'left',
                'index' => 'fax_number',
                'type' => 'text'
            ),
            'email_address' => array(
                'header' => Mage::helper('epicor_comm')->__('Email Address'),
                'align' => 'left',
                'index' => 'email_address',
                'type' => 'text'
            )
        );

        $showColumns = explode(',', Mage::getStoreConfig('epicor_comm_locations/admin/grid_columns'));

        foreach ($columns as $columnId => $info) {
            if (in_array($columnId, $showColumns)) {
                $this->addColumn($columnId, $info);
            }
        }

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
                    'field' => 'id',
                    'confirm' => Mage::helper('epicor_comm')->__('Are you sure you want to delete this Location? This cannot be undone')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('locationid');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_comm')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_comm')->__('Delete selected locations?')
        ));

        return $this;
    }

}
