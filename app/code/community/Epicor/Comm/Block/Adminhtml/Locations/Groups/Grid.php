<?php

class Epicor_Comm_Block_Adminhtml_Locations_Groups_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('groups_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/location_groupings')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Id'),
                'align' => 'left',
                'index' => 'id',
                'type' => 'number'
            ));
        $this->addColumn('group_name',
            array(
                'header' => Mage::helper('epicor_comm')->__('Group Name'),
                'align' => 'left',
                'index' => 'group_name',
                'type' => 'text'
            ));
        $this->addColumn('group_expandable', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Group Expandable'),
                'align' => 'left',
                'index' => 'group_expandable',
                'type' => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
            ));
        $this->addColumn('show_aggregate_stock',
            array(
                'header' => Mage::helper('epicor_comm')->__('Show Aggregate Stock'),
                'align' => 'left',
                'index' => 'show_aggregate_stock',
                'type' => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
            ));
        $this->addColumn('enabled',
            array(
                'header' => Mage::helper('epicor_comm')->__('Enabled'),
                'align' => 'left',
                'index' => 'enabled',
                'type' => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
            ));
        $this->addColumn('order',
            array(
                'header' => Mage::helper('epicor_comm')->__('Sort Order'),
                'align' => 'left',
                'index' => 'order',
                'type' => 'text'
            ));
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('epicor_comm')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('group_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_comm')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_comm')->__('Delete selected Groups?')
        ));

        return $this;
    }

}
